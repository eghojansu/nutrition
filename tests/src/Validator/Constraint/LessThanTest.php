<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\LessThan;

class LessThanTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new LessThan([
            'value' => 3,
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(2)->validate()->isValid());
        $this->assertFalse($constraint->setValue(3)->validate()->isValid());
        $this->assertFalse($constraint->setValue(4)->validate()->isValid());
    }
}
