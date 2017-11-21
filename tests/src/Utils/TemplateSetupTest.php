<?php

namespace Nutrition\Test;

use MyTestCase;
use Nutrition\Utils\TemplateSetup;

class TemplateSetupTest extends MyTestCase
{
    private $setup;

    protected function setUp()
    {
        $this->setup = new TemplateSetup();
    }

    public function testAddPrefix()
    {
        $this->setup->set('key', 'App');
        $this->setup->addPrefix('key', 'Test');
        $this->assertEquals('Test - App', $this->setup->get('key'));

        $this->setup->set('key', 'App');
        $this->setup->prefixKey('Test');
        $this->assertEquals('Test - App', $this->setup->get('key'));
    }

    public function testSetGet()
    {
        $set = 'value';
        $expected = $set;

        $this->setup->set('key', $set);
        $this->assertEquals($expected, $this->setup->get('key'));

        $this->setup->key = $set;
        $this->assertEquals($expected, $this->setup->key);

        $this->setup['key'] = $set;
        $this->assertEquals($expected, $this->setup['key']);

        $this->setup->setKey($set);
        $this->assertEquals($expected, $this->setup->getKey($set));

        $this->setup->setKey($set);
        $this->assertEquals($expected, $this->setup->isKey($set));
    }

    public function testExists()
    {
        $this->assertFalse($this->setup->exists('key'));
        $this->setup->set('key', 'oke');
        $this->assertTrue($this->setup->exists('key'));
    }

    public function testExistsArray()
    {
        $this->assertFalse(isset($this->setup['key']));
        $this->setup['key'] = 'oke';
        $this->assertTrue(isset($this->setup['key']));
    }

    public function testExistsMagic()
    {
        $this->assertFalse(isset($this->setup->key));
        $this->setup->key = 'oke';
        $this->assertTrue(isset($this->setup->key));
    }

    public function testClear()
    {
        $this->setup->set('key', 'oke');
        $this->assertTrue($this->setup->exists('key'));
        $this->setup->clear('key');
        $this->assertFalse($this->setup->exists('key'));
    }

    public function testClearMagic()
    {
        $this->setup->key = 'oke';
        $this->assertTrue(isset($this->setup->key));
        unset($this->setup->key);
        $this->assertFalse(isset($this->setup->key));
    }

    public function testClearArray()
    {
        $this->setup['key'] = 'oke';
        $this->assertTrue(isset($this->setup['key']));
        unset($this->setup['key']);
        $this->assertFalse(isset($this->setup['key']));
    }
}
