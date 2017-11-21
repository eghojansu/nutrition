<?php

namespace Nutrition\Test\Console;

use MyTestCase;
use Nutrition\Console\StringParser;

class StringParserTest extends MyTestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(StringParser::class, StringParser::create('<info>Info</>'));
    }

    public function testParse()
    {
        $this->assertInstanceOf(StringParser::class, StringParser::create('<info>Info</>')->parse());
    }

    public function testGetParsed()
    {
        $this->assertEquals([
            ['colored'=>'Info','original'=>'Info','newline'=>false],
        ], StringParser::create('Info')->getParsed());
        $this->assertEquals([
            ['colored'=>'Info','original'=>'Info','newline'=>true],
            ['colored'=>'Warning','original'=>'Warning','newline'=>false],
        ], StringParser::create("Info\nWarning")->getParsed());
    }
}
