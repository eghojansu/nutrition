<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Test\Fixture\MyMenu;

class MenuTest extends MyTestCase
{
    private $menu;
    private $base;

    protected function setUp()
    {
        $this->menu = new MyMenu();
        $this->base = Base::instance();
        $this->base['QUIET'] = true;
        $this->base->route('GET @homepage: /', function(Base $base) {
            echo 'OKE';
        });
    }

    public function testGetCurrent()
    {
        $this->base->mock('GET /');
        $this->assertEquals('homepage', $this->menu->getCurrent());
    }

    public function testSetCurrent()
    {
        $this->menu->setCurrent('test_route');
        $this->assertEquals('test_route', $this->menu->getCurrent());
    }

    public function testIsActive()
    {
        $this->base->mock('GET /');
        $this->assertTrue($this->menu->isActive('homepage'));
    }

    public function testGetMenu()
    {
        $this->assertTrue(is_array($this->menu->getMenu('test')));
    }
}
