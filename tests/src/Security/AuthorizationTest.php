<?php

namespace Nutrition\Test\Security;

use Base;
use MyTestCase;
use Nutrition\Security\Authorization;
use Nutrition\Security\User;
use Nutrition\Security\UserManager;
use Registry;

class AuthorizationTest extends MyTestCase
{
    private $authorization;

    protected function setUp()
    {
        $this->authorization = new Authorization();
    }

    protected function tearDown()
    {
        $base = Base::instance();
        $base->clear('SESSION');
        $base->clear('SECURITY');
        $this->authorization = null;
        Registry::clear(UserManager::class);
    }

    public function testGuard()
    {
        $user = new User('Username', 'password', ['ROLE_ADMIN'], false, false);
        $app = Base::instance();
        $app['QUIET'] = true;
        $app->set('SECURITY.firewalls', [
            'main' => [
                'path' => '^/secure',
                'roles' => ['ROLE_SUPER_ADMIN'],
            ],
        ]);
        $app->route('GET @secure: /secure', function(Base $app) {
            $this->authorization->guard('login');

            echo 'Secure';
        });
        $app->route('GET @login: /auth/login', function(Base $app) {
            echo 'Login';
        });
        $app->set('ONREROUTE', function($url) use ($app) {
            $app->set('reroute', $url);
        });
        $app->mock('GET /secure');

        $this->assertStringEndsWith('/auth/login', $app['reroute']);
    }

    public function testIsGranted()
    {
        $this->assertTrue($this->authorization->isGranted('ROLE_ANONYMOUS'));
        $this->assertFalse($this->authorization->isGranted('ROLE_SUPER_ADMIN'));
    }

    public function testIsGranted2()
    {
        $user = new User('Username', 'password', ['ROLE_ADMIN'], false, false);
        UserManager::instance()->setUser($user);

        $this->assertTrue($this->authorization->isGranted('ROLE_ADMIN'));
        $this->assertFalse($this->authorization->isGranted('ROLE_SUPER_ADMIN'));
    }

    public function testIsGranted3()
    {
        $user = new User('Username', 'password', ['ROLE_SUPER_ADMIN'], false, false);
        UserManager::instance()->setUser($user);
        Base::instance()->set('SECURITY.role_hierarchy', [
            'ROLE_ADMIN' => 'ROLE_USER',
            'ROLE_SUPER_ADMIN' => 'ROLE_ADMIN',
        ]);

        $this->assertTrue($this->authorization->isGranted('ROLE_SUPER_ADMIN'));
        $this->assertTrue($this->authorization->isGranted('ROLE_ADMIN'));
        $this->assertTrue($this->authorization->isGranted('ROLE_USER'));
        $this->assertFalse($this->authorization->isGranted('ROLE_NONE'));
    }
}
