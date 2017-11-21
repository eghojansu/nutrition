<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Url;

class UrlTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Url();

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('http://www.example.com')->validate()->isValid());
        $this->assertFalse($constraint->setValue('http:/example.com')->validate()->isValid());
        $this->assertFalse($constraint->setValue('342.com')->validate()->isValid());
        $this->assertFalse($constraint->setValue('ww.com.cm')->validate()->isValid());
    }
}
