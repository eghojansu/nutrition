<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Utils\PaginationSetup;

class PaginationSetupTest extends MyTestCase
{
    private $setup;

    protected function setUp()
    {
        $this->setup = new PaginationSetup();
        $base = Base::instance();
        $base['QUIET'] = true;
        $base->route('GET @paginate: /paginate/@param', function() {
            var_dump($_GET);
        });
        $base->mock('GET /paginate/param');
    }

    public function testGetPerpage()
    {
        $this->assertEquals(10, $this->setup->getPerpage());
    }

    public function testSetPerpage()
    {
        $this->setup->setPerpage(15);
        $this->assertEquals(15, $this->setup->getPerpage());
    }

    public function testGetAdjacent()
    {
        $this->assertEquals(3, $this->setup->getAdjacent());
    }

    public function testSetAdjacent()
    {
        $this->setup->setAdjacent(15);
        $this->assertEquals(15, $this->setup->getAdjacent());
    }

    public function testGetRoute()
    {
        $this->assertEquals('paginate', $this->setup->getRoute());
    }

    public function testSetRoute()
    {
        $this->setup->setRoute('update');
        $this->assertEquals('update', $this->setup->getRoute());
    }

    public function testGetRouteParams()
    {
        $this->assertEquals(['param'=>'param'], $this->setup->getRouteParams());
    }

    public function testSetRouteParams()
    {
        $this->setup->setRouteParams(['param'=>'update']);
        $this->assertEquals(['param'=>'update'], $this->setup->getRouteParams());
    }

    public function testGetRequestPage()
    {
        $this->assertEquals(1, $this->setup->getRequestPage());
    }

    public function testSetRequestPage()
    {
        $this->setup->setRequestPage(12);
        $this->assertEquals(12, $this->setup->getRequestPage());
    }

    public function testGetRequestArg()
    {
        $this->assertEquals(null, $this->setup->getRequestArg('no-exists'));
    }

    public function testGetPageArgName()
    {
        $this->assertEquals('page', $this->setup->getPageArgName());
    }

    public function testSetPageArgName()
    {
        $this->setup->setPageArgName('p');
        $this->assertEquals('p', $this->setup->getPageArgName());
    }

    public function testPath()
    {
        $this->assertStringEndsWith('/paginate/param?page=3', $this->setup->path(false, 3));
    }
}
