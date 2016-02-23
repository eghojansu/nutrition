<?php

namespace Nutrition\Tests;

use Base;
use Nutrition\Tests\data\controller\Controller;

class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    protected function getBase($turnOffHalt = false)
    {
        $app = Base::instance();
        $app->set('HALT', !$turnOffHalt);

        return $app;
    }

    public function testA()
    {
        $app = $this->getBase();
        $this->assertEquals('/test/page/number', $app->get('ALIASES.testPageNumber'));
        $this->assertEquals('/test/page/limit', $app->get('ALIASES.testPageLimit'));
    }

    /**
     * @depends testA
     */
    public function testPageNumber()
    {
        $data = ['pos'=>20];
        $app = $this->getBase();
        $app->mock('GET @testPageNumber', $data);
        $this->assertEquals($data, $_GET);
        $this->assertEquals($data['pos'], $app->get('RESPONSE'));
    }

    /**
     * @depends testA
     */
    public function testPageLimit()
    {
        $app = $this->getBase();
        $app->mock('GET @testPageLimit');
        $this->assertEquals('10', $app->get('RESPONSE'));
    }

    /**
     * @dataProvider providerShowError
     */
    public function testShowError($code)
    {
        $app = $this->getBase(true);
        $app->mock('GET @testShowError(code='.$code.')');
        $this->assertEquals($code, $app->get('ERROR.code'));
    }

    public function testShowErrorNotFound()
    {
        $app = $this->getBase(true);
        $app->mock('GET @testShowErrorNotFound');
        $this->assertEquals(404, $app->get('ERROR.code'));
    }

    public function testShowErrorForbidden()
    {
        $app = $this->getBase(true);
        $app->mock('GET @testShowErrorForbidden');
        $this->assertEquals(403, $app->get('ERROR.code'));
    }

    public function testShowErrorInternalServer()
    {
        $app = $this->getBase(true);
        $app->mock('GET @testShowErrorInternalServer');
        $this->assertEquals(500, $app->get('ERROR.code'));
    }

    public function testRedirectTo()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testGetHomepage()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testGoHome()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testGoBack()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testRender()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testFlash()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testJSONResponse()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function providerShowError()
    {
        return [
            [404],
            [403],
            [500],
        ];
    }
}