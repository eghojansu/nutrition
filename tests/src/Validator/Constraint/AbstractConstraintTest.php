<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Test\Fixture\CustomConstraint;

class AbstractConstraintTest extends MyTestCase
{
    private $constraint;

    protected function setUp()
    {
        $this->constraint = new CustomConstraint(['groups'=>['Update']]);
    }

    public function testGetMessages()
    {
        $this->assertEquals(null, $this->constraint->getMessages());
    }

    public function testGetGroups()
    {
        $this->assertEquals(['Update'], $this->constraint->getGroups());
    }

    public function testSetValue()
    {
        $this->assertEquals($this->constraint, $this->constraint->setValue('this'));
    }

    public function testIsValid()
    {
        $this->assertTrue($this->constraint->isValid());
    }

    public function testValidate()
    {
        $this->assertEquals($this->constraint, $this->constraint->validate());
        $this->assertFalse($this->constraint->isValid());
        $this->assertEquals($this->constraint, $this->constraint->setValue('value')->validate());
        $this->assertTrue($this->constraint->isValid());
    }
}
