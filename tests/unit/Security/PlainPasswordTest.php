<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\Security;

use Nutrition\Security\PlainPassword;

class PlainPasswordTest extends \PHPUnit_Framework_TestCase
{
    public function getObject()
    {
        return new PlainPassword;
    }

    /**
     * @dataProvider providerPlainText
     */
    public function testEncode($plain)
    {
        $obj = $this->getObject();
        $hash = $obj->encode($plain);
        $this->assertEquals($plain, $hash);

        return $hash;
    }

    /**
     * @dataProvider providerPlainText
     */
    public function testVerify($plain)
    {
        $obj = $this->getObject();
        $hash = $obj->encode($plain);
        $this->assertTrue($obj->verify($plain, $hash));
    }

    public function providerPlaintext()
    {
        return [
            ['plain'],
            ['text'],
            ['admin'],
            ['foo-bar'],
            ['kabur'],
        ];
    }
}