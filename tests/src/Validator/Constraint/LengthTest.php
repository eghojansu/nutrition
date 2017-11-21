<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Length;

class LengthTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Length([
            'max' => 10,
            'min' => 3
        ]);

        $this->assertTrue($constraint->setValue('000')->validate()->isValid());
        $this->assertTrue($constraint->setValue('0000')->validate()->isValid());
        $this->assertTrue($constraint->setValue('0000000000')->validate()->isValid());
        $this->assertFalse($constraint->setValue('00000000000')->validate()->isValid());
        $this->assertFalse($constraint->setValue('00')->validate()->isValid());
    }
}
