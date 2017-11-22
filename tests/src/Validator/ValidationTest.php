<?php

namespace Nutrition\Test\Validator;

use MyTestCase;
use Nutrition\Validator\Constraint\NotBlank;
use Nutrition\Validator\Validation;
use Nutrition\Validator\ViolationList;

class ValidationTest extends MyTestCase
{
    public function testAdd()
    {
        $validator = new Validation();

        $this->assertCount(0, $validator->getConstraints());

        $validator->add('username', new NotBlank());

        $this->assertCount(1, $validator->getConstraints());
    }

    public function testValidate()
    {
        $validator = new Validation([
            'username' => new NotBlank(),
        ], [
            'username' => 'not blank'
        ]);
        $violations = $validator->validate();

        $this->assertInstanceOf(ViolationList::class, $violations);
        $this->assertFalse($violations->hasViolation());

        $violations = $validator->validate(['Default'], [
            'username' => 'not blank  '
        ]);

        $this->assertInstanceOf(ViolationList::class, $violations);
        $this->assertFalse($violations->hasViolation());
        $this->assertEquals(['username'=>'not blank'], $validator->getData());


        $validator = new Validation([
            'username' => new NotBlank([
                'normalizer' => function($data) { return $data.'-transformed'; }
            ]),
        ], [
            'username' => 'not blank'
        ]);
        $violations = $validator->validate();

        $this->assertInstanceOf(ViolationList::class, $violations);
        $this->assertFalse($violations->hasViolation());
        $this->assertEquals(['username'=>'not blank-transformed'], $validator->getData());
    }

    public function testAfter()
    {
        $validator = new Validation([
            'username' => new NotBlank(),
        ], [
            'username' => 'not blank'
        ]);
        $validator->after(function($data, $violations) {
            $data['username'] = 'username';

            return $data;
        });
        $violations = $validator->validate();

        $this->assertInstanceOf(ViolationList::class, $violations);
        $this->assertEquals(['username'=>'username'], $validator->getData());
    }

    public function testGetConstraints()
    {
        $validator = new Validation([], []);

        $this->assertCount(0, $validator->getConstraints());

        $validator->add('username', new NotBlank());

        $this->assertCount(1, $validator->getConstraints());
    }

    public function testGetData()
    {
        $validator = new Validation([
            'username' => new NotBlank(),
        ], [
            'username' => 'not blank'
        ]);
        $violations = $validator->validate();

        $this->assertInstanceOf(ViolationList::class, $violations);
        $this->assertEquals(['username'=>'not blank'], $validator->getData());
    }
}
