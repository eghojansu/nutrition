<?php

namespace Nutrition\Test\Validator;

use MyTestCase;
use Nutrition\Validator\ViolationList;

class ViolationListTest extends MyTestCase
{
    private $violations;

    protected function setUp()
    {
        $this->violations = new ViolationList();
    }

    public function testAdd()
    {
        $this->violations->add('key', 'Message');

        $this->assertTrue($this->violations->exists('key'));
        $this->assertEquals(['Message'], $this->violations->get('key'));
    }

    public function testSet()
    {
        $this->violations->set('key', 'Message');

        $this->assertTrue($this->violations->exists('key'));
        $this->assertEquals(['Message'], $this->violations->get('key'));
    }

    public function testClear()
    {
        $this->violations->set('key', 'Message');

        $this->assertTrue($this->violations->exists('key'));
        $this->assertEquals(['Message'], $this->violations->get('key'));
        $this->violations->clear('key');
        $this->assertFalse($this->violations->exists('key'));
    }

    public function testExists()
    {
        $this->assertFalse($this->violations->exists('key'));

        $this->violations->add('key', 'Message');

        $this->assertTrue($this->violations->exists('key'));
    }

    public function testGet()
    {
        $this->assertEquals([], $this->violations->get('key'));

        $this->violations->add('key', 'Message');

        $this->assertEquals(['Message'], $this->violations->get('key'));
    }

    public function testAll()
    {
        $this->assertEquals([], $this->violations->all());

        $this->violations->add('key', 'Message');

        $this->assertEquals(['key'=>['Message']], $this->violations->all());
    }

    public function testHasViolation()
    {
        $this->assertFalse($this->violations->hasViolation());

        $this->violations->add('key', 'Message');

        $this->assertTrue($this->violations->hasViolation());
    }

    public function testHasNoViolation()
    {
        $this->assertTrue($this->violations->hasNoViolation());

        $this->violations->add('key', 'Message');

        $this->assertFalse($this->violations->hasNoViolation());
    }
}
