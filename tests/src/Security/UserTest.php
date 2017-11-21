<?php

namespace Nutrition\Test\Security;

use MyTestCase;
use Nutrition\Security\User;

class UserTest extends MyTestCase
{
    private $user;
    private $userData = [
        'username'=>'Username',
        'password'=>'password',
        'roles'=>['ROLE_ADMIN'],
        'expired'=>false,
        'blocked'=>false,
    ];

    protected function setUp()
    {
        $this->user = new User(
            $this->userData['username'],
            $this->userData['password'],
            $this->userData['roles'],
            $this->userData['expired'],
            $this->userData['blocked']
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals('Username', $this->user->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals('password', $this->user->getPassword());
    }

    public function testGetRoles()
    {
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());
    }

    public function testIsExpired()
    {
        $this->assertFalse($this->user->isExpired());
    }

    public function testIsBlocked()
    {
        $this->assertFalse($this->user->isBlocked());
    }
}
