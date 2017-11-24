<?php

namespace Nutrition\Test\Validator\Constraint;

use Base;
use MyTestCase;
use Nutrition\Constraint\UserPassword;
use Nutrition\Security\PlainPasswordEncoder;
use Nutrition\Security\Security;
use Nutrition\Security\UserManager;

class UserPasswordTest extends MyTestCase
{
    protected function setUp()
    {
        Base::instance()->set('SECURITY.password_encoder', PlainPasswordEncoder::class);
    }

    protected function tearDown()
    {
        Base::instance()->clear('SECURITY');
        Base::instance()->clear('SESSION');
        UserManager::instance()->logout();
    }

    public function testValidate()
    {
        UserManager::instance()->setUser(Security::instance()->getUserClass(true, [
            'Username',
            'Password',
            ['ROLE_ADMIN'],
            false,
            false
        ]));
        $constraint = new UserPassword();

        $this->assertTrue($constraint->validate()->isValid());
        $this->assertTrue($constraint->setValue('Password')->validate()->isValid());
        $this->assertFalse($constraint->setValue('invalid')->validate()->isValid());
    }
}
