<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Equal;

class EqualTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Equal([
            'value' => 'this',
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('this')->validate()->isValid());
        $this->assertFalse($constraint->setValue('that')->validate()->isValid());
    }
}
