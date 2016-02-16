<?php

namespace Nutrition\Tests\Security;

use Base;
use Nutrition\Security\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;

    public function setUp()
    {
        $this->obj = new User;
    }

    public function testVerify()
    {
        $this->assertTrue($this->obj->verify('password'));
    }

    public function testAuthenticate()
    {
        $this->assertTrue($this->obj->authenticate('username', 'password'));
        $this->assertFalse($this->obj->authenticate('username', 'invalid password'));

        return $this->obj;
    }

    /**
     * @depends testAuthenticate
     */
    public function testIsGuest(User $obj)
    {
        $this->assertFalse($obj->isGuest());
    }

    /**
     * @depends testAuthenticate
     */
    public function testWasLogged(User $obj)
    {
        $this->assertTrue($obj->wasLogged());
    }

    /**
     * @expectedException Nutrition\InvalidConfigurationException
     */
    public function testNoProviderException()
    {
        $old = Base::instance()->get('SECURITY.provider');
        Base::instance()->set('SECURITY.provider', null);
        new User;
    }
}