<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Ip;

class IpTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Ip();

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('127.0.0.1')->validate()->isValid());
        $this->assertTrue($constraint->setValue('192.168.43.1')->validate()->isValid());
        $this->assertFalse($constraint->setValue('192')->validate()->isValid());
        $this->assertFalse($constraint->setValue('192:168:43:1')->validate()->isValid());
    }
}
