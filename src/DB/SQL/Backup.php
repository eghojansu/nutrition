<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\DB\SQL;

use Nutrition;
use Nutrition\DB\BackupProviderInterface;
use Base;
use Web;
use PDO;

/**
 * Backup/restore with insert sql statement
 */
class Backup
{
    /**
     * Information provider
     * @var Nutrition\DB\BackupProviderInterface
     */
    protected $provider;

    /**
     * Get Backup list
     * @return array
     */
    public function getBackupList()
    {
        $app = Base::instance();
        $data = Nutrition::dirContent($this->provider->getBackupDir());
        foreach ($data as $key => $value) {
            $data[$key] = basename($value);
        }

        return $data;
    }

    /**
     * Download backup file
     * @param  string $file
     * @return bool|null
     */
    public function download($file)
    {
        $filepath = $this->provider->getBackupDir().$file;

        if (!file_exists($filepath)) {
            return false;
        }

        $mime = null;
        $throttle = 512;
        $force = true;

        Web::instance()->send($filepath, $mime, $throttle, $force);
    }

    /**
     * Delete file
     * @param  string $file
     * @return bool
     */
    public function delete($file)
    {
        $filepath = $this->provider->getBackupDir().$file;
        $result = unlink($filepath);

        return $result;
    }

    /**
     * Backup database, store in server
     * @return string
     */
    public function backup()
    {
        $pdo = $this->provider->getPDO();
        $tmpfile = $this->provider->getTempFilepath();
        $fileStream = fopen($tmpfile, 'w');
        fwrite($fileStream, $this->provider->getMetaData());
        $tableExported = 0;
        foreach ($this->provider->getTables() as $table => $columns) {
            $fields = is_array($columns)?implode(',', $columns):($columns?:'*');
            $sql = 'select '.$fields.' from '.$table;
            $query = $pdo->prepare($sql);
            $query->execute();

            $counter = 0;
            $rowCount = 0;
            $maxRow = $query->rowCount();
            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                ++$counter;
                ++$rowCount;
                $line = '';
                if (1 === $counter) {
                    $line .= 'insert into `'.$table.'` values'.PHP_EOL;
                }
                $line .= '('.implode(',', $this->validateTypes($row)).')';
                if (100 === $counter || $rowCount === $maxRow) {
                    $line .= ';';
                    $counter = 0;
                } else {
                    $line .= ',';
                }
                $line .= PHP_EOL;

                fwrite($fileStream, $line);
            }
            $tableExported += $maxRow?1:0;

            fwrite($fileStream, PHP_EOL.PHP_EOL);
        }
        fclose($fileStream);

        rename($tmpfile, $this->provider->getFilepath());

        return $tableExported > 0;
    }

    /**
     * Restore data
     * @param  string $file
     * @return bool
     */
    public function restore($file)
    {
        $filepath = $this->provider->validateBackupFile($file);
        if (!$filepath) {
            return false;
        }

        $app = Base::instance();
        $pdo = $this->provider->getPDO();

        $tables = array_keys($this->provider->getTables());
        $sql = 'delete from `'.implode('`; delete from `', $tables).'`;';
        $pdo->exec($sql);

        $sql = $app->read($filepath);
        $pdo->exec($sql);

        return true;
    }

    /**
     * Make dir
     */
    protected function makeDir()
    {
        if (!file_exists($this->provider->getBackupDir())) {
            mkdir($this->provider->getBackupDir());
        }
    }

    /**
     * Validate data type
     * @param  array  $data
     * @return array
     */
    protected function validateTypes(array $data)
    {
        foreach ($data as $key => $value) {
            switch (gettype($value)) {
                case 'NULL':
                    $data[$key] = 'NULL';
                    break;
                case 'boolean':
                case 'integer':
                    $data[$key] = (int) $value;
                    break;
                default:
                    $data[$key] = "'".$value."'";
                    break;
            }
        }

        return $data;
    }

    public function __construct(BackupProviderInterface $provider)
    {
        $this->provider = $provider;
        $this->makeDir();
    }
}