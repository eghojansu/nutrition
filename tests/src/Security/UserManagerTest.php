<?php

namespace Nutrition\Test\Security;

use Base;
use MyTestCase;
use Nutrition\Security\User;
use Nutrition\Security\UserManager;

class UserManagerTest extends MyTestCase
{
    private $userManager;
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
        $this->userManager = new UserManager();
        $this->user = new User(
            $this->userData['username'],
            $this->userData['password'],
            $this->userData['roles'],
            $this->userData['expired'],
            $this->userData['blocked']
        );
    }

    protected function tearDown()
    {
        $base = Base::instance();
        $base->clear('SESSION');
        $base->clear('SECURITY');
    }

    public function testIsLogin()
    {
        $this->assertFalse($this->userManager->isLogin());
        $this->userManager->setUser($this->user);
        $this->assertTrue($this->userManager->isLogin());
    }

    public function testLogout()
    {
        $this->userManager->setUser($this->user);
        $this->assertTrue($this->userManager->isLogin());
        $this->userManager->logout();
        $this->assertFalse($this->userManager->isLogin());
    }

    public function testSetUser()
    {
        $this->assertFalse($this->userManager->isLogin());
        $this->userManager->setUser($this->user);
        $this->assertTrue($this->userManager->isLogin());
    }

    public function testGetUser()
    {
        $this->assertNull($this->userManager->getUser());
        $this->userManager->setUser($this->user);
        $this->assertEquals($this->user, $this->userManager->getUser());
    }
}
