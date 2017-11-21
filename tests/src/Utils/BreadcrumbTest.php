<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Utils\Breadcrumb;
use Nutrition\Utils\GroupChecker;

class BreadcrumbTest extends MyTestCase
{
    private $breadcrumb;

    protected function setUp()
    {
        $this->breadcrumb = new Breadcrumb();
    }

    public function testClear()
    {
        $this->breadcrumb->add('route');
        $this->assertCount(1, $this->breadcrumb->getContent());
        $this->breadcrumb->clear();
        $this->assertCount(0, $this->breadcrumb->getContent());
    }

    public function testSetRoot()
    {
        $this->breadcrumb->setRoot('root_route');
        $content = $this->breadcrumb->getContent();
        $this->assertEquals('root_route', $content[0]['route']);
    }

    public function testAdd()
    {
        $this->breadcrumb->add('first_route');
        $content = $this->breadcrumb->getContent();
        $this->assertEquals('first_route', $content[0]['route']);
    }

    public function testAddCurrentRoute()
    {
        $base = Base::instance();
        $base->route('GET @homepage: /', function(Base $base) {
            $this->breadcrumb->addCurrentRoute();
            echo 'Sample';
        });
        $base['QUIET'] = true;
        $base->mock('GET /');

        $content = $this->breadcrumb->getContent();
        $this->assertEquals('homepage', $content[0]['route']);
        $this->assertEquals('Homepage', $content[0]['label']);
    }

    public function testAddGroup()
    {
        $base = Base::instance();
        $base->route('GET @homepage: /', function(Base $base) {
            $group = new GroupChecker(['One'=>'one','Two'=>'two'],'two');
            $this->breadcrumb->addGroup($group);

            echo 'Sample';
        });
        $base['QUIET'] = true;
        $base->mock('GET /');

        $content = $this->breadcrumb->getContent();
        $this->assertEquals('homepage', $content[1]['route']);
        $this->assertEquals('Two', $content[1]['label']);
    }

    public function testGetContent()
    {
        $this->breadcrumb->add('first_route');
        $content = $this->breadcrumb->getContent();
        $this->assertEquals('first_route', $content[0]['route']);
    }

    public function testIsLast()
    {
        $this->breadcrumb->add('first_route');
        $this->assertTrue($this->breadcrumb->isLast(0));
    }
}
