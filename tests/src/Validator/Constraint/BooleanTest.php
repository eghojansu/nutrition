<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Boolean;

class BooleanTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Boolean();

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('1')->validate()->isValid());
        $this->assertTrue($constraint->setValue('on')->validate()->isValid());
        $this->assertTrue($constraint->setValue('true')->validate()->isValid());
        $this->assertTrue($constraint->setValue(true)->validate()->isValid());
        $this->assertFalse($constraint->setValue('0')->validate()->isValid());
        $this->assertFalse($constraint->setValue(false)->validate()->isValid());
    }
}
