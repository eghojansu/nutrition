<?php

namespace Nutrition\Security;

use Base;
use Nutrition\App;
use Prefab;


class Security extends Prefab
{
    /**
     * @see getClassName
     */
    public function getPasswordEncoder($createClass = true, array $params = null)
    {
        return App::instance()->getClassName(
            'SECURITY.password_encoder',
            BcryptPasswordEncoder::class,
            PasswordEncoderInterface::class,
            $createClass,
            $params
        );
    }

    /**
     * @see getClassName
     */
    public function getUserProvider($createClass = true, array $params = null)
    {
        return App::instance()->getClassName(
            'SECURITY.user_provider',
            InMemoryUserProvider::class,
            UserProviderInterface::class,
            $createClass,
            $params
        );
    }

    /**
     * @see getClassName
     */
    public function getUserClass($createClass = true, array $params = null)
    {
        return App::instance()->getClassName(
            'SECURITY.user_class',
            User::class,
            UserInterface::class,
            $createClass,
            $params
        );
    }
}
