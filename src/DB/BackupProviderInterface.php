<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\DB;

interface BackupProviderInterface
{
    /**
     * Get backup filepath, ensure prefix with DIRECTORY_SEPARATOR
     * @return string
     */
    public function getBackupDir();

    /**
     * Get temporary filepath
     * @return string
     */
    public function getTempFilepath();

    /**
     * Get filepath
     * @return string
     */
    public function getFilepath();

    /**
     * Get meta data to prepend in backup file
     * @return string
     */
    public function getMetaData();

    /**
     * Get tables and columns information
     *     ex:
     *         [
     *             'table1'=>['col1','col2','col3'],
     *             'table2'=>['col1','col2','col3'],
     *         ]
     * @return array
     */
    public function getTables();

    /**
     * Get PDO instance
     * @return PDO
     */
    public function getPDO();

    /**
     * Validate backup file then return the filepath
     * @param  string $file
     * @return string
     */
    public function validateBackupFile($file);
}