<?php

namespace Nutrition\Test\SQL;

use Base;
use DB\SQL\Mapper;
use MyTestCase;
use Nutrition\SQL\ConnectionBuilder;
use Nutrition\Test\Fixture\Database;
use Nutrition\Test\Fixture\SampleEntity;
use Nutrition\Utils\Pagination;

class MapperTest extends MyTestCase
{
    private $entity;
    private $config;

    protected function setUp()
    {
        $this->config = Database::getConfig();
        Base::instance()->set('DATABASE', $this->config);

        $this->entity = SampleEntity::create();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(SampleEntity::class, SampleEntity::create());
    }

    public function testTableName()
    {
        $this->assertEquals('SampleEntities', $this->entity->tableName());
    }

    public function testCreatePagination()
    {
        Database::insertSampleEntityTable();

        $pagination = $this->entity->createpagination();

        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertEquals(5, $pagination->allRecordCount);
    }

    public function testMagicMethodCall()
    {
        Database::resetDatabase();
        $this->assertEquals(0, $this->entity->count());

        $this->entity->set('Name', 'Record 1');
        $this->entity->save();
        $this->assertEquals(1, $this->entity->count());
        $this->assertEquals('Record 1', $this->entity->findone()->Name);
        $this->assertEquals('Record 1', $this->entity->findOneByName('Record 1')->Name);
        $this->assertCount(1, $this->entity->findByName('Record 1'));
    }

    public function testConnection()
    {
        $this->assertEquals($this->entity->connection(), ConnectionBuilder::instance()->getConnection());
    }
}
