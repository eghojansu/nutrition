<?php

namespace Nutrition\Test\Utils;

use MyTestCase;
use Nutrition\Utils\CommonUtil;

class CommonUtilTest extends MyTestCase
{
    public function testDecide()
    {
        $this->assertEquals('a', CommonUtil::decide(true, 'a','b'));
        $this->assertEquals('b', CommonUtil::decide(false, 'a','b'));
    }

    public function testDateSQL()
    {
        $this->assertEquals('10-10-2017', CommonUtil::dateSQL('2017-10-10 10:10:10'));
    }

    public function testTrueFalse()
    {
        $this->assertEquals('True', CommonUtil::trueFalse('true'));
        $this->assertEquals('False', CommonUtil::trueFalse('false'));
    }

    public function testOnOff()
    {
        $this->assertEquals('On', CommonUtil::onOff('on'));
        $this->assertEquals('Off', CommonUtil::onOff('off'));
    }

    public function testYesNo()
    {
        $this->assertEquals('Yes', CommonUtil::yesNo('on'));
        $this->assertEquals('No', CommonUtil::yesNo('off'));
    }

    public function testPostValue()
    {
        $this->assertNull(CommonUtil::postValue('nothing'));
    }
    public function testRandom()
    {
        $this->assertEquals(6, strlen(CommonUtil::random()));
        $this->assertEquals(10,strlen( CommonUtil::random(10)));
    }

    public function testEndsWith()
    {
        $this->assertTrue(CommonUtil::endsWith('#', 'this#'));
        $this->assertFalse(CommonUtil::endsWith('#', 'this'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(CommonUtil::startsWith('#', '#this'));
        $this->assertFalse(CommonUtil::startsWith('#', 'this'));
    }

    public function testLowerLabel()
    {
        $labels = ['Zero','One','Two','Three'];

        $this->assertEquals('Zero', CommonUtil::lowerLabel(0, $labels));
        $this->assertEquals('One', CommonUtil::lowerLabel(1, $labels));
        $this->assertEquals('Two', CommonUtil::lowerLabel(2, $labels));
        $this->assertEquals('Three', CommonUtil::lowerLabel(3, $labels));
        $this->assertEquals('Three', CommonUtil::lowerLabel(10, $labels));
    }

    /**
     * @dataProvider lengthProvider
     */
    public function testLength($assert, $length, $str, $compare)
    {
        $this->assertEquals($assert, CommonUtil::length($length, $str, $compare));
    }

    public function lengthProvider()
    {
        return [
            [true, 10, 'stringstri', '='],
            [true, 10, 'stringstring', '>'],
            [false, 10, 'stringstri', '>'],
            [true, 10, 'stringstri', '>='],
            [true, 10, 'stringstr', '<'],
            [true, 10, 'stringstri', '<='],
        ];
    }

    /**
     * snake_case to Title Case
     * @param  string $str
     * @return string
     */
    public function testTitleCase()
    {
        $this->assertEquals('Title Case', CommonUtil::titleCase('title_case'));
    }

    public function testSnakeCase()
    {
        $this->assertEquals('snake_case', CommonUtil::snakeCase('SnakeCase'));
    }

    public function testPascalCase()
    {
        $this->assertEquals('PascalCase', CommonUtil::pascalCase('pascal_case'));
    }

    public function testCamelCase()
    {
        $this->assertEquals('camelCase', CommonUtil::camelCase('camel_case'));
    }

    public function testDump()
    {
        $this->expectOutputString(<<<EXPECTED
<pre>
--------------------------------------------------------------------------------
string(6) "output"

--------------------------------------------------------------------------------
</pre>
EXPECTED
);
        CommonUtil::dump('output');
    }

    public function testMajorVersion()
    {
        $this->assertEquals('1.0.0', CommonUtil::majorVersion('1.3.0'));
        $this->assertEquals('v1.0.0', CommonUtil::majorVersion('v1.3.0'));
        $this->assertEquals('v1.0.0-beta', CommonUtil::majorVersion('v1.3.0-beta'));
        $this->assertEquals('v1.0.0-beta-1', CommonUtil::majorVersion('v1.3.0-beta-1'));
    }

    public function testMinorVersion()
    {
        $this->assertEquals('1.3.0', CommonUtil::minorVersion('1.3.0'));
        $this->assertEquals('v1.3.0', CommonUtil::minorVersion('v1.3.0'));
        $this->assertEquals('v1.3.0-beta', CommonUtil::minorVersion('v1.3.0-beta'));
        $this->assertEquals('v1.3.0-beta-1', CommonUtil::minorVersion('v1.3.0-beta-1'));
    }
}
