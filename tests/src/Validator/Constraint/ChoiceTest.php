<?php

namespace Nutrition\Test\Validator\Constraint;

use MyTestCase;
use Nutrition\Validator\Constraint\Choice;

class ChoiceTest extends MyTestCase
{
    public function testValidate()
    {
        $constraint = new Choice([
            'choices' => ['a','b','c'],
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('a')->validate()->isValid());
        $this->assertTrue($constraint->setValue('b')->validate()->isValid());
        $this->assertTrue($constraint->setValue('c')->validate()->isValid());
        $this->assertFalse($constraint->setValue('d')->validate()->isValid());


        $constraint = new Choice([
            'choices' => ['a','b','c'],
            'multiple' => true,
        ]);

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue(['a'])->validate()->isValid());
        $this->assertTrue($constraint->setValue('b')->validate()->isValid());
        $this->assertTrue($constraint->setValue(['b','c'])->validate()->isValid());
        $this->assertFalse($constraint->setValue('d')->validate()->isValid());
    }
}
