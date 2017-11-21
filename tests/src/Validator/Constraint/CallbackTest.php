<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Callback;

class CallbackTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Callback([
            'callback' => function($value) {
                return $value === 'value';
            }
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('value')->validate()->isValid());
        $this->assertFalse($constraint->setValue('on')->validate()->isValid());
    }
}
