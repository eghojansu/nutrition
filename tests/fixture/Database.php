<?php

namespace Nutrition\Test\Fixture;

use PDO;

class Database
{
    protected static $pdo;
    protected static $config = [
        'host'=>'localhost',
        'name'=>'test_fatfree_nutrition',
        'username'=>'root',
        'password'=>'root',
    ];

    public static function getConfig()
    {
        return self::$config;
    }

    public static function pdo()
    {
        if (null === self::$pdo) {
            $dsn = 'mysql:host='.self::$config['host'];
            $pdo = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password']
            );
            $pdo->exec('CREATE DATABASE IF NOT EXISTS '.self::$config['name']);
            $pdo->exec('USE '.self::$config['name']);
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS SampleEntities (
    ID INT NOT NULL,
    Name VARCHAR(200) NOT NULL,
    PRIMARY KEY (ID)
);
SQL;
            $pdo->exec($sql);

            self::$pdo = $pdo;
        }

        return self::$pdo;
    }

    public static function resetDatabase()
    {
        self::pdo()->exec('DELETE FROM SampleEntities');
    }

    public static function insertSampleEntityTable()
    {
        $sql = <<<SQL
INSERT INTO SampleEntities VALUES
    (1, 'Record 1'),
    (2, 'Record 2'),
    (3, 'Record 3'),
    (4, 'Record 4'),
    (5, 'Record 5')
SQL;
        self::resetDatabase();
        self::pdo()->exec($sql);
    }
}
