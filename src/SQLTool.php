<?php

namespace Nutrition;

use Base;
use DB\SQL;
use Prefab;
use RuntimeException;
use PDO;

class SQLTool extends Prefab
{
    protected $db;

    public function __construct(SQL $db = null)
    {
        $this->db = $db?:Base::instance()->get('DB.SQL');
    }

    /**
     * Get database size in mb
     * @return int
     */
    public function size()
    {
        $sql = <<<SQL
select round(sum(data_length + index_length) / 1024 / 1024, 1) "size_mb"
from information_schema.tables where table_schema = :db
SQL;
        $result = $this->db->exec($sql, [
            ':db'=>$this->db->name(),
            ]);

        return $result?$result[0]['size_mb']:0;
    }

    /**
     * Import sql file
     * @param  string $file
     */
    public function import($file)
    {
        $handle = fopen($file, "rb");
        if ($handle) {
            $limit = 2048;
            $counter = 0;
            $buffer = '';
            while (($line = fgets($handle, $limit)) !== false) {
                if ('--' === substr($line, 0, 2) ||
                    empty(trim($line))
                    ) {
                    continue;
                }

                $counter += strlen($line);
                $buffer .= $line;

                if ($counter >= $limit && ';' === substr(rtrim($line), -1, 1)) {
                    $this->db->exec($buffer);
                    $buffer = '';
                    $counter = 0;
                }
            }
            if (!feof($handle)) {
                throw new RuntimeException('Unexpected readfile error', 1);
            }
            if ($buffer) {
                $this->db->exec($buffer);
            }
            fclose($handle);
        }

        return $this;
    }

    /**
     * Export table
     * @param  string $table
     * @param  string $file
     * @param  boolean $fileAppend
     * @param  string|array $columns
     * @param  string $meta    file meta
     * @return boolean
     */
    public function export($table, $file, $fileAppend = false, $columns = '*', $meta = null)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sql-export');

        $base = Base::instance();
        $meta = $meta?(is_callable($meta)?call_user_func_array($meta, [$table]):$meta):'';
        $quoted = $this->db->quote($table);

        fwrite($tempFile, $meta);

        $tableExported = 0;
        $fields = is_array($columns)?$columns:$base->split($columns?:'*');
        foreach ($fields as $key => $value) {
            $fields[$key] = $this->db->quote($value);
        }
        $sql = 'SELECT '.implode(',', $fields).' FROM '.$quoted;
        $query = $this->pdo()->prepare($sql);
        $query->execute();

        $counter = 0;
        $rowCount = 0;
        $maxRow = $query->rowCount();
        while ($row = $query->fetch(PDO::FETCH_NUM)) {
            ++$counter;
            ++$rowCount;
            $line = '';
            if (1 === $counter) {
                $line .= 'INSERT INTO '.$quoted.' VALUES'.PHP_EOL;
            }
            $line .= '('.implode(',', $this->validateExportTypes($row)).')';
            if (100 === $counter || $rowCount === $maxRow) {
                $line .= ';';
                $counter = 0;
            } else {
                $line .= ',';
            }
            $line .= PHP_EOL;

            fwrite($tempFile, $line);
        }

        fwrite($tempFile, PHP_EOL.PHP_EOL);
        fclose($tempFile);

        $result = $base->write($file, $base->read($tempFile), $fileAppend);
        unlink($tempFile);

        return $result;
    }

    /**
     * Validate data type
     * @param  array  $data
     * @return array
     */
    protected function validateExportTypes(array $data)
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
}
