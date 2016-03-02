<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\DB\SQL;

/**
 * DB\SQL constructor
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

use Base;
use DB\SQL;
use Nutrition\InvalidConfigurationException;
use Registry;

class Connection
{
    /**
     * Get connection
     * adopt from http://meedo.in
     * Require configuration like below:
     *     database
     *         default:
     *             driver: mysql
     *             server: localhost
     *             dbname: dbname
     *             username: root
     *             password: null
     *             port: 3306
     *             charset: UTF8
     *             socket: null
     *             file: /path/to/database.file
     *         default2:
     *             driver: mysql
     *             server: localhost
     *             dbname: dbname2
     *             username: root
     *             password: null
     *             port: 3306
     *             charset: UTF8
     *             socket: null
     *             file: /path/to/database.file
     * @param  string $connection
     * @return DB\SQL
     */
    public static function getConnection($connection = 'default')
    {
        $key = 'DATABASE.'.$connection;
        if (!Registry::exists($key)) {
            $config = (Base::instance()->get($key)?:[]) + [
                'driver'   => null,
                'dbname'   => null,
                'server'   => null,
                'username' => null,
                'password' => null,
                'port'     => 3306,
                'charset'  => null,
                'socket'   => null,
                'file'     => null,
                ];

            $isPort   = isset($config['port']) && is_int($config['port'] * 1);
            $driver   = strtolower($config['driver']);

            switch ($driver) {
                case 'mariadb':
                    $driver = 'mysql';
                case 'mysql':
                    if ($config['socket']) {
                        $dsn = $driver . ':unix_socket=' . $config['socket'] . ';dbname=' . $config['dbname'];
                    } else {
                        $dsn = $driver . ':host=' . $config['server'] . ($isPort ? ';port=' . $config['port'] : '') . ';dbname=' . $config['dbname'];
                    }
                    break;
                case 'pgsql':
                    $dsn = $driver . ':host=' . $config['server'] . ($isPort ? ';port=' . $config['port'] : '') . ';dbname=' . $config['dbname'];
                    break;
                case 'sybase':
                    $dsn = 'dblib:host=' . $config['server'] . ($isPort ? ':' . $config['port'] : '') . ';dbname=' . $config['dbname'];
                    break;
                case 'oracle':
                    $dbname = $config['server'] ?
                        '//' . $config['server'] . ($isPort ? ':' . $config['port'] : ':1521') . '/' . $config['dbname'] :
                        $config['dbname'];
                    $dsn = 'oci:dbname=' . $dbname . ($config['charset'] ? ';charset=' . $config['charset'] : '');
                    break;
                case 'mssql':
                    $dsn = strstr(PHP_OS, 'WIN') ?
                        'sqlsrv:server=' . $config['server'] . ($isPort ? ',' . $config['port'] : '') . ';database=' . $config['dbname'] :
                        'dblib:host=' . $config['server'] . ($isPort ? ':' . $config['port'] : '') . ';dbname=' . $config['dbname'];
                    break;
                case 'sqlite':
                    $dsn = $driver . ':' . $config['file'];
                    $config['username'] = null;
                    $config['password'] = null;
                    break;
                default:
                    throw new InvalidConfigurationException('Invalid '.$key.' configuration');
            }

            return Registry::set($key, new SQL($dsn, $config['username'], $config['password']));
        }

        return Registry::get($key);
    }
}