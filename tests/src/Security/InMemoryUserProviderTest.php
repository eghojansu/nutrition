<?php

namespace Nutrition\Test\Security;

use Base;
use MyTestCase;
use Nutrition\Security\InMemoryUserProvider;
use Nutrition\Security\PlainPasswordEncoder;
use Nutrition\Security\User;

class InMemoryUserProviderTest extends MyTestCase
{
    private $provider;
    private $userData = [
        'username'=>'admin',
        'password'=>'password',
        'roles'=>['ROLE_ADMIN'],
        'expired'=>false,
        'blocked'=>false,
    ];

    protected function setUp()
    {
        $base = Base::instance();
        $base->set('SECURITY.password_encoder', PlainPasswordEncoder::class);
        $base->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $this->provider = new InMemoryUserProvider();
    }

    protected function tearDown()
    {
        $base = Base::instance();
        $base->clear('SESSION');
        $base->clear('SECURITY');
    }

    public function testLoadByUsername()
    {
        $expected = new User(
            $this->userData['username'],
            $this->userData['password'],
            $this->userData['roles'],
            $this->userData['expired'],
            $this->userData['blocked']
        );

        $this->assertEquals($expected, $this->provider->loadByUsername($this->userData['username']));
        $this->assertNull($this->provider->loadByUsername('not-exists-username'));
    }
}
