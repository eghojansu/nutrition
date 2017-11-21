<?php

namespace Nutrition\Test\Console;

use MyTestCase;
use Base;
use Nutrition\Test\Fixture\MyCommand;

class CommandTest extends MyTestCase
{
    private $base;

    protected function setUp()
    {
        $this->base = Base::instance();
        $this->base['QUIET'] = true;
        MyCommand::registerSelf($this->base);
    }

    public function testWriteAction()
    {
        $expected = 'Line';
        $this->base->mock('GET /cmd/write [cli]');
        $this->assertEquals($expected, $this->base['RESPONSE']);
    }

    public function testWritelnAction()
    {
        $expected = 'Line'.PHP_EOL;
        $this->base->mock('GET /cmd/writeln [cli]');
        $this->assertEquals($expected, $this->base['RESPONSE']);
    }

    public function testWriteTableAction()
    {
        $expected = '--------------' . PHP_EOL .
                    ' Col 1  Col 2 ' . PHP_EOL .
                    '--------------' . PHP_EOL .
                    ' C1R1   C2R1  ' . PHP_EOL .
                    ' C1R2   C2R2  ' . PHP_EOL .
                    '--------------' . PHP_EOL ;

        $this->base->mock('GET /cmd/writetable [cli]');
        $this->assertEquals($expected, $this->base['RESPONSE']);
    }

    public function testWithOptionAction()
    {
        $expected = 'TEST';
        $this->base->mock('GET /cmd/withoption?option=TEST [cli]');
        $this->assertEquals($expected, $this->base['RESPONSE']);
    }

    public function testHasOptionAction()
    {
        $expected = 'TRUE';
        $this->base->mock('GET /cmd/hasoption?option=TEST [cli]');
        $this->assertEquals($expected, $this->base['RESPONSE']);
    }
}
