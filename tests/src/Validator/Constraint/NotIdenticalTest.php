<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\NotIdentical;

class NotIdenticalTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new NotIdentical([
            'value' => 'this',
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertFalse($constraint->setValue('this')->validate()->isValid());
        $this->assertTrue($constraint->setValue('that')->validate()->isValid());
    }
}
