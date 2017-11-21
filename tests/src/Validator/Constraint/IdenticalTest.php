<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Identical;

class IdenticalTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Identical([
            'value' => 'this',
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('this')->validate()->isValid());
        $this->assertFalse($constraint->setValue('that')->validate()->isValid());
    }
}
