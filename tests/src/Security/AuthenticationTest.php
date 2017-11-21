<?php

namespace Nutrition\Test\Security;

use Base;
use Registry;
use MyTestCase;
use Nutrition\Security\Authentication;
use Nutrition\Security\PlainPasswordEncoder;
use Nutrition\Security\User;
use Nutrition\Security\UserInterface;
use Nutrition\Security\UserManager;
use Nutrition\Test\Fixture\UserClassNotImplementUserInterface;

class AuthenticationTest extends MyTestCase
{
    private $authentication;
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
        $base['QUIET'] = true;
        $base->set('SECURITY.password_encoder', PlainPasswordEncoder::class);
        $base->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $base->route('GET @dashboard: /dashboard', function() {
            echo 'Dashboard';
        });
        $this->authentication = new Authentication();
    }

    protected function tearDown()
    {
        $base = Base::instance();
        $base->clear('SESSION');
        $base->clear('SECURITY');
        Registry::clear(UserManager::class);
    }

    protected function createUser()
    {
        return new User(
            $this->userData['username'],
            $this->userData['password'],
            $this->userData['roles'],
            $this->userData['expired'],
            $this->userData['blocked']
        );
    }

    public function testAttempt()
    {
        $actual = $this->authentication->attempt(
            $this->userData['username'],
            $this->userData['password']
        );

        $this->assertTrue($actual);
    }

    /**
     * @expectedException Nutrition\Security\UsernameNotFoundException
     */
    public function testAttempt2()
    {
        $actual = $this->authentication->attempt(
            'not-exists-username',
            $this->userData['password']
        );
    }

    /**
     * @expectedException Nutrition\Security\InvalidPasswordException
     */
    public function testAttempt3()
    {
        $actual = $this->authentication->attempt(
            $this->userData['username'],
            'invalid-password'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAttempt4()
    {
        Base::instance()->set('SECURITY.user_class', UserClassNotImplementUserInterface::class);
        $actual = $this->authentication->attempt(
            $this->userData['username'],
            $this->userData['password']
        );
    }

    /**
     * @expectedException Nutrition\Security\ExpiredUserException
     */
    public function testAttempt5()
    {
        $this->userData['expired'] = true;
        Base::instance()->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $actual = $this->authentication->attempt(
            $this->userData['username'],
            $this->userData['password']
        );
    }

    /**
     * @expectedException Nutrition\Security\BlockedUserException
     */
    public function testAttempt6()
    {
        $this->userData['blocked'] = true;
        Base::instance()->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $actual = $this->authentication->attempt(
            $this->userData['username'],
            $this->userData['password']
        );
    }

    public function testHandleAttempt()
    {
        $actual = $this->authentication->handleAttempt(
            true,
            $this->userData['username'],
            $this->userData['password'],
            'dashboard'
        );
        $this->assertNull($actual);
    }

    public function testHandleAttempt2()
    {
        $expected = 'Invalid credentials';
        $actual = $this->authentication->handleAttempt(
            true,
            'invalid-username',
            $this->userData['password'],
            'dashboard'
        );
        $this->assertEquals($expected, $actual);
    }

    public function testHandleAttempt3()
    {
        Base::instance()->set('SECURITY.user_class', UserClassNotImplementUserInterface::class);
        $expected = 'SECURITY.user_class must be an instance of '.UserInterface::class;
        $actual = $this->authentication->handleAttempt(
            true,
            $this->userData['username'],
            $this->userData['password'],
            'dashboard'
        );
        $this->assertEquals($expected, $actual);
    }

    public function testHandleAttempt4()
    {
        $this->userData['expired'] = true;
        Base::instance()->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $expected = 'Your account was expired';
        $actual = $this->authentication->handleAttempt(
            true,
            $this->userData['username'],
            $this->userData['password'],
            'dashboard'
        );
        $this->assertEquals($expected, $actual);
    }

    public function testHandleAttempt5()
    {
        $this->userData['blocked'] = true;
        Base::instance()->set('SECURITY.users', [
            'admin' => $this->userData
        ]);
        $expected = 'Your account was blocked';
        $actual = $this->authentication->handleAttempt(
            true,
            $this->userData['username'],
            $this->userData['password'],
            'dashboard'
        );
        $this->assertEquals($expected, $actual);
    }
}
