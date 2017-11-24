<?php

namespace Nutrition\Test;

use Base;
use MyTestCase;
use Nutrition\App;
use Nutrition\Security\BcryptPasswordEncoder;
use Nutrition\Security\InMemoryUserProvider;
use Nutrition\Security\PlainPasswordEncoder;
use Nutrition\Security\User;
use Nutrition\Security\UserInterface;
use Nutrition\Test\Fixture\FileSystemHelper;
use Nutrition\Test\Fixture\User as UserFixture;
use Nutrition\Test\Fixture\UserClassNotImplementUserInterface;
use Nutrition\Test\Fixture\UserProvider;
use Nutrition\Utils\FileSystem;
use RuntimeException;

class AppTest extends MyTestCase
{
    private $app;
    private $logDir;

    protected function setUp()
    {
        $this->app = new App();
        $this->logDir = FileSystemHelper::logDir();
    }

    protected function tearDown()
    {
        Base::instance()->clear('SECURITY');
        $fs = new FileSystem($this->logDir);
        $fs->removeDir();
    }

    public function testLog()
    {
        $base = Base::instance();
        $base->set('LOGS', $this->logDir.'/');
        $base->set('LOG_FILE', 'log.txt');

        $this->app->log('Test log');
        $this->assertFileExists($file = $base['LOGS'].$base['LOG_FILE']);
        $this->assertContains('Test log', $base->read($file));
    }

    public function testError()
    {
        $base = Base::instance();
        $base->set('LOGS', $this->logDir.'/');
        $base->set('LOG_FILE', 'log.txt');

        ob_start();
        $this->app->error($base, []);
        ob_end_clean();

        $this->assertFileExists($base['LOGS'].$base['LOG_FILE']);
    }

    public function testRegisterErrorHandler()
    {
        $base = Base::instance();

        $this->app->registerErrorHandler();

        $this->assertEquals([$this->app,'error'], $base['ONERROR']);
    }

    public function testGetClassName()
    {
        $key = 'SECURITY.user_class';

        $result = $this->app->getClassName($key, User::class, UserInterface::class);

        $this->assertEquals(User::class, $result);
    }

    public function testGetClassName2()
    {
        $key = 'SECURITY.user_class';
        Base::instance()->set($key, UserFixture::class);

        $result = $this->app->getClassName($key, User::class, UserInterface::class);

        $this->assertEquals(UserFixture::class, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetClassName3()
    {
        $key = 'SECURITY.user_class';
        Base::instance()->set($key, UserClassNotImplementUserInterface::class);

        $result = $this->app->getClassName($key, User::class, UserInterface::class);
    }

    public function testGetClassName4()
    {
        $key = 'SECURITY.user_class';

        $actual = $this->app->getClassName($key, User::class, UserInterface::class, true, [
            'username'=>'Username',
            'password'=>'password',
            'roles'=>['ROLE_ADMIN'],
            'expired'=>false,
            'blocked'=>false,
        ]);

        $this->assertInstanceOf(User::class, $actual);
    }

    public function testGetClassName5()
    {
        $key = 'SECURITY.user_class';
        Base::instance()->set($key, UserFixture::class);

        $actual = $this->app->getClassName($key, User::class, UserInterface::class, true, [
            'username'=>'Username',
            'password'=>'password',
            'roles'=>['ROLE_ADMIN'],
            'expired'=>false,
            'blocked'=>false,
        ]);

        $this->assertInstanceOf(UserFixture::class, $actual);
    }
}
