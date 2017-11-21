<?php

namespace Nutrition\Test;

use MyTestCase;
use Nutrition\Test\Fixture\MyMagicService;

class MagicServiceTest extends MyTestCase
{
    private $magic;

    protected function setUp()
    {
        $this->magic = new MyMagicService;
    }

    public function testSetGet()
    {
        $set = 'value';
        $expected = $set;

        $this->magic->set('key', $set);
        $this->assertEquals($expected, $this->magic->get('key'));

        $this->magic->key = $set;
        $this->assertEquals($expected, $this->magic->key);

        $this->magic['key'] = $set;
        $this->assertEquals($expected, $this->magic['key']);

        $this->magic->setKey($set);
        $this->assertEquals($expected, $this->magic->getKey($set));

        $this->magic->setKey($set);
        $this->assertEquals($expected, $this->magic->isKey($set));
    }

    public function testExists()
    {
        $this->assertFalse($this->magic->exists('key'));
        $this->magic->set('key', 'oke');
        $this->assertTrue($this->magic->exists('key'));
    }

    public function testExistsArray()
    {
        $this->assertFalse(isset($this->magic['key']));
        $this->magic['key'] = 'oke';
        $this->assertTrue(isset($this->magic['key']));
    }

    public function testExistsMagic()
    {
        $this->assertFalse(isset($this->magic->key));
        $this->magic->key = 'oke';
        $this->assertTrue(isset($this->magic->key));
    }

    public function testClear()
    {
        $this->magic->set('key', 'oke');
        $this->assertTrue($this->magic->exists('key'));
        $this->magic->clear('key');
        $this->assertFalse($this->magic->exists('key'));
    }

    public function testClearMagic()
    {
        $this->magic->key = 'oke';
        $this->assertTrue(isset($this->magic->key));
        unset($this->magic->key);
        $this->assertFalse(isset($this->magic->key));
    }

    public function testClearArray()
    {
        $this->magic['key'] = 'oke';
        $this->assertTrue(isset($this->magic['key']));
        unset($this->magic['key']);
        $this->assertFalse(isset($this->magic['key']));
    }
}
