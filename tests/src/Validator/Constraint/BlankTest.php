<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Blank;

class BlankTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Blank();

        $this->assertTrue($constraint->validate()->isValid());
        $constraint->setValue('not blank');
        $this->assertFalse($constraint->validate()->isValid());
    }
}
