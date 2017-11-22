<?php

namespace Nutrition\Test\Fixture;

use Nutrition\SQL\ConnectionBuilder;
use PDO;

class Database
{
    public static function getConfig()
    {
        return [
            'host'=>'localhost',
            'name'=>'test_fatfree_nutrition',
            'username'=>'root',
            'password'=>'root',
        ];
    }

    public static function createPDO(array $config)
    {
        $dsn = 'mysql:host='.$config['host'];
        return new PDO(
            $dsn,
            $config['username'],
            $config['password']
        );
    }

    public static function create(array $config)
    {
        self::createPDO($config)->exec('CREATE DATABASE IF NOT EXISTS '.$config['name']);
    }

    public static function drop(array $config)
    {
        self::createPDO($config)->exec('DROP DATABASE IF EXISTS '.$config['name']);
    }

    public static function createSampleEntityTable(ConnectionBuilder $builder)
    {
        $sql = <<<SQL
CREATE TABLE SampleEntities (
    ID INT NOT NULL AUTO_INCREMENT,
    Name VARCHAR(200) NOT NULL,
    PRIMARY KEY (ID)
) ENGINE = MyISAM
SQL;
        $builder->getConnection()->exec($sql);
    }

    public static function insertSampleEntityTable(ConnectionBuilder $builder)
    {
        $sql = <<<SQL
INSERT INTO SampleEntities (Name) VALUES
    ('Record 1'),
    ('Record 2'),
    ('Record 3'),
    ('Record 4'),
    ('Record 5')
SQL;
        $builder->getConnection()->exec($sql);
    }
}
