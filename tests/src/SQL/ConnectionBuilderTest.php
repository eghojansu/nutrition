<?php

namespace Nutrition\Test\SQL;

use Base;
use DB\SQL;
use MyTestCase;
use Nutrition\SQL\ConnectionBuilder;
use Nutrition\Test\Fixture\Database;
use PDO;

class ConnectionBuilderTest extends MyTestCase
{
    private $builder;
    private $config;

    protected function setUp()
    {
        $this->config = Database::getConfig();
        Base::instance()->set('DATABASE', $this->config);
        Database::create($this->config);

        $this->builder = new ConnectionBuilder();
    }

    protected function tearDown()
    {
        Database::drop($this->config);
        $this->builder = null;
    }

    public function testGetConfig()
    {
        $this->assertEquals($this->config, $this->builder->getConfig());
        $this->assertEquals($this->config['username'], $this->builder->getConfig('username'));
        $this->assertEquals($this->config['not-exists'], $this->builder->getConfig('not-exists'), 'default value');
    }

    public function testSetConfig()
    {
        $config = [
            'host'=>'localhost',
            'name'=>'test_fatfree_nutrition',
            'username'=>'root',
            'password'=>'',
        ];
        $this->builder->setConfig($config);
        $this->assertEquals($config, $this->builder->getConfig());
        $this->assertEquals($config['password'], $this->builder->getConfig('password'));
    }

    public function testGetConnection()
    {
        $connection = $this->builder->getConnection();

        $this->assertInstanceOf(SQL::class, $connection);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetConnection2()
    {
        $this->builder->setConfig('password',null)->getConnection();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetConnection3()
    {
        $this->builder->setConfig('username',null)->getConnection();
    }

    public function testGetPDO()
    {
        $pdo = $this->builder->pdoWithoutDB();

        $this->assertInstanceOf(PDO::class, $pdo);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetPDO2()
    {
        $this->builder->setConfig('password',null)->pdoWithoutDB();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetPDO3()
    {
        $this->builder->setConfig('username',null)->pdoWithoutDB();
    }

    public function testGetStatus()
    {
        $this->assertEquals(100, $this->builder->getStatus());
    }

    public function testIsHealthy()
    {
        $this->assertTrue($this->builder->isHealthy());
    }

    public function testGetSize()
    {
        $this->assertEquals(0, $this->builder->getSize());
    }

    public function testGetTables()
    {
        $this->assertEquals([], $this->builder->getTables());
    }
}
