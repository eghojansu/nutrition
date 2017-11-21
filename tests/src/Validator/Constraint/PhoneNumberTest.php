<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\PhoneNumber;

class PhoneNumberTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new PhoneNumber();

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('+62852291511175')->validate()->isValid());
        $this->assertTrue($constraint->setValue('+628522915111')->validate()->isValid());
        $this->assertTrue($constraint->setValue('+6285229151')->validate()->isValid());
        $this->assertTrue($constraint->setValue('0852291511175')->validate()->isValid());
        $this->assertTrue($constraint->setValue('08522915111')->validate()->isValid());
        $this->assertTrue($constraint->setValue('085229151')->validate()->isValid());
        $this->assertFalse($constraint->setValue('1234')->validate()->isValid());
        $this->assertFalse($constraint->setValue('nothp')->validate()->isValid());
        $this->assertFalse($constraint->setValue('ldskdls')->validate()->isValid());
    }
}
