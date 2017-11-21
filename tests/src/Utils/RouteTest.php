<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Utils\Route;

class RouteTest extends MyTestCase
{
    private $route;
    private $base;

    protected function setUp()
    {
        $this->route = new Route();
        $this->base = Base::instance();
        $this->base['QUIET'] = true;
        $this->base->route('GET @homepage: /homepage/@param', function(Base $base) {
            echo 'OKE';
        });
    }

    public function testPath()
    {
        $this->assertStringEndsWith('/path', $this->route->path('/path'));
    }

    public function testBuild()
    {
        $this->assertStringEndsWith('/homepage/param', $this->route->build('homepage',['param'=>'param']));
        $this->assertStringEndsWith('/homepage/param', $this->route->build('homepage',['param'=>'param','page'=>2]));
        $this->assertStringEndsWith('/homepage/param?page=2', $this->route->build('homepage',['param'=>'param'],['page'=>2]));
    }

    public function testCurrentPath()
    {
        $this->base->mock('GET /homepage/param');
        $this->assertStringEndsWith('/homepage/param', $this->route->currentPath());
    }
}
