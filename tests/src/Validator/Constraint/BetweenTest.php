<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Between;

class BetweenTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Between([
            'min' => 3,
            'max' => 5,
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(3)->validate()->isValid());
        $this->assertTrue($constraint->setValue(4)->validate()->isValid());
        $this->assertTrue($constraint->setValue(5)->validate()->isValid());
        $this->assertFalse($constraint->setValue(2)->validate()->isValid());
        $this->assertFalse($constraint->setValue(6)->validate()->isValid());
    }
}
