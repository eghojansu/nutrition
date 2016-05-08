<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\data;

use Nutrition\DB\BackupProviderInterface;
use Nutrition\DB\SQL\Connection;
use Base;

class BackupProvider implements BackupProviderInterface
{
    /**
     * Get backup filepath, ensure prefix with DIRECTORY_SEPARATOR
     * @return string
     */
    public function getBackupDir()
    {
        return realpath(__DIR__).'/tmp/';
    }

    /**
     * Get temporary filepath
     * @return string
     */
    public function getTempFilepath()
    {
        return $this->getBackupDir().Base::instance()->hash(microtime()).'.tmp.sql';
    }

    /**
     * Get filepath
     * @return string
     */
    public function getFilepath()
    {
        return $this->getBackupDir().'backup-'.date('Ymd').'.sql';
    }

    /**
     * Get meta data to prepend in backup file
     * @return string
     */
    public function getMetaData()
    {
        return '';
    }

    /**
     * Get tables and columns information
     *     ex:
     *         [
     *             'table1'=>['col1','col2','col3'],
     *             'table2'=>['col1','col2','col3'],
     *         ]
     * @return array
     */
    public function getTables()
    {
        return ['category'=>null,'product'=>null];
    }

    /**
     * Get PDO instance
     * @return PDO
     */
    public function getPDO()
    {
        return Connection::getConnection()->pdo();
    }

    /**
     * Validate backup file then return the filepath
     * @param  string $file
     * @return string
     */
    public function validateBackupFile($file)
    {
        $filepath = $this->getBackupDir().$file;

        return $filepath;
    }
}