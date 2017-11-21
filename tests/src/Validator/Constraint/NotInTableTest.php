<?php

namespace Nutrition\Test\Validator\Constraint;

use Base;
use MyTestCase;
use Nutrition\SQL\ConnectionBuilder;
use Nutrition\Test\Fixture\Database;
use Nutrition\Test\Fixture\SampleEntity;
use Nutrition\Validator\Constraint\NotInTable;

class NotInTableTest extends MyTestCase
{
    private $entity;
    private $config;

    protected function setUp()
    {
        $this->config = Database::getConfig();
        Base::instance()->set('database', $this->config);
        Database::create($this->config);
        $builder = ConnectionBuilder::instance();
        Database::createSampleEntityTable($builder);
        Database::insertSampleEntityTable($builder);

        $this->entity = SampleEntity::create();
    }

    protected function tearDown()
    {
        Database::drop($this->config);
    }

    public function testValidate()
    {
        $constraint = new NotInTable([
            'mapper' => $this->entity
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(13)->validate()->isValid());
        $this->assertFalse($constraint->setValue(1)->validate()->isValid());

        $constraint = new NotInTable([
            'mapper' => SampleEntity::class
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(13)->validate()->isValid());
        $this->assertFalse($constraint->setValue(1)->validate()->isValid());
    }
}
