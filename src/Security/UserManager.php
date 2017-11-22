<?php

namespace Nutrition\Security;

use Base;
use Exception;
use InvalidArgumentException;
use Nutrition\App;
use Nutrition\Security\UserInterface;
use Prefab;

/**
 * User Manager
 */
class UserManager extends Prefab
{
    const SESSION_USER = 'SESSION.user';
    const SESSION_LOGIN = 'SESSION.login';

    /** @var Nutrition\Security\UserInterface */
    private $user;

    /**
     * Is user login
     * @return boolean
     */
    public function isLogin()
    {
        return Base::instance()->get(static::SESSION_LOGIN) ?: false;
    }

    /**
     * Clear user
     */
    public function logout()
    {
        Base::instance()->clear('SESSION');
    }

    /**
     * Set current user
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        Base::instance()->mset([
            static::SESSION_USER => $user->getUsername(),
            static::SESSION_LOGIN => true,
        ]);
        $this->user = $user;
    }

    /**
     * Get user, load from session if needed
     * @return UserInterface
     */
    public function getUser()
    {
        if (null === $this->user && $this->isLogin()) {
            $username = Base::instance()->get(static::SESSION_USER);
            $this->user = Security::instance()->getUserProvider()->loadByUsername($username);

            try {
                Authentication::instance()->checkUser($this->user);
            } catch(Exception $e) {
                $this->user = null;
            }
        }

        return $this->user;
    }
}
