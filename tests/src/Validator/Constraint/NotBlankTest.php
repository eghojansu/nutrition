<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\NotBlank;

class NotBlankTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new NotBlank();

        $this->assertFalse($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('not blank')->validate()->isValid());
    }
}
