<?php

namespace Nutrition\Test;

use Base;
use MyTestCase;
use Nutrition\Security\BcryptPasswordEncoder;
use Nutrition\Security\InMemoryUserProvider;
use Nutrition\Security\PlainPasswordEncoder;
use Nutrition\Security\Security;
use Nutrition\Security\User;
use Nutrition\Test\Fixture\User as UserFixture;
use Nutrition\Test\Fixture\UserProvider;

class SecurityTest extends MyTestCase
{
    private $security;

    protected function setUp()
    {
        $this->security = new Security();
    }

    protected function tearDown()
    {
        $base = Base::instance();
        $base->clear('SESSION');
        $base->clear('SECURITY');
    }

    /**
     * @dataProvider getUserClassProvider
     * @param  string $userClass set user class
     * @param  string $expected expected class
     * @return void
     */
    public function testGetUserClass($userClass, $expected)
    {
        Base::instance()->set('SECURITY.user_class', $userClass);

        $actual = $this->security->getUserClass(false);
        $this->assertEquals($expected, $actual);

        $actual = $this->security->getUserClass(true, [
            'username'=>'Username',
            'password'=>'password',
            'roles'=>['ROLE_ADMIN'],
            'expired'=>false,
            'blocked'=>false,
        ]);
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @dataProvider getUserProviderProvider
     * @param  string $class set user class
     * @param  string $expected expected class
     * @return void
     */
    public function testGetUserProvider($class, $expected)
    {
        Base::instance()->set('SECURITY.user_provider', $class);

        $actual = $this->security->getUserProvider(false);
        $this->assertEquals($expected, $actual);

        $actual = $this->security->getUserProvider();
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @dataProvider getPasswordEncoderProvider
     * @param  string $class set user class
     * @param  string $expected expected class
     * @return void
     */
    public function testGetPasswordEncoder($class, $expected)
    {
        Base::instance()->set('SECURITY.password_encoder', $class);

        $actual = $this->security->getPasswordEncoder(false);
        $this->assertEquals($expected, $actual);

        $actual = $this->security->getPasswordEncoder();
        $this->assertInstanceOf($expected, $actual);
    }

    public function getUserClassProvider()
    {
        return [
            [null, User::class],
            [UserFixture::class, UserFixture::class],
        ];
    }

    public function getUserProviderProvider()
    {
        return [
            [null, InMemoryUserProvider::class],
            [UserProvider::class, UserProvider::class],
        ];
    }

    public function getPasswordEncoderProvider()
    {
        return [
            [null, BcryptPasswordEncoder::class],
            [PlainPasswordEncoder::class, PlainPasswordEncoder::class],
        ];
    }
}
