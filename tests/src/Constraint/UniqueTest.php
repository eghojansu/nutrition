<?php

namespace Nutrition\Test\Validator\Constraint;

use Base;
use MyTestCase;
use Nutrition\Constraint\Unique;
use Nutrition\Test\Fixture\Database;
use Nutrition\Test\Fixture\SampleEntity;

class UniqueTest extends MyTestCase
{
    private $entity;
    private $config;

    protected function setUp()
    {
        $this->config = Database::getConfig();
        Base::instance()->set('DATABASE', $this->config);
        Database::insertSampleEntityTable();

        $this->entity = SampleEntity::create();
    }

    public function testValidate()
    {
        $constraint = new Unique([
            'mapper' => $this->entity
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(13)->validate()->isValid());
        $this->assertFalse($constraint->setValue(1)->validate()->isValid());

        $constraint = new Unique([
            'mapper' => SampleEntity::class
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(13)->validate()->isValid());
        $this->assertFalse($constraint->setValue(1)->validate()->isValid());
    }
}
