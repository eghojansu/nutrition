<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Utils\FlashMessage;

class FlashMessageTest extends MyTestCase
{
    private $flash;

    protected function setUp()
    {
        $this->flash = new FlashMessage();
    }

    protected function tearDown()
    {
        Base::instance()->clear('SESSION');
    }

    public function testAdd()
    {
        $this->flash->add('info', 'Message');
        $this->flash->add('info', 'Other Message');

        $this->assertEquals(['info'=>['Message','Other Message']], Base::instance()->get('SESSION.FLASH'));
    }

    public function testGet()
    {
        $this->flash->add('info', 'Message');
        $this->flash->add('info', 'Other Message');

        $this->assertEquals(['Message','Other Message'], $this->flash->get('info'));
    }

    public function testAll()
    {
        $this->flash->add('info', 'Message');
        $this->flash->add('info', 'Other Message');
        $this->flash->add('warning', 'Other2 Message');

        $this->assertEquals(['info'=>['Message','Other Message'],'warning'=>['Other2 Message']], $this->flash->all());
    }
}
