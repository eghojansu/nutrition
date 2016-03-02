<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\data;

use Nutrition\Security\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * Authenticate with username
     * @param  string $username of course, it can be email too
     * @return bool
     */
    public function authenticate($username)
    {
        return true;
    }

    /**
     * Load user data
     * @param  mixed $id user_id
     */
    public function loadUserData($id)
    {
        // loading...
    }

    /**
     * Get session id
     * @return string
     */
    public function getSessionID()
    {
        return 'sessid';
    }

    /**
     * Get password
     * @return  string
     */
    public function getPassword()
    {
        return 'password';
    }

    /**
     * Get user id
     * @return  int
     */
    public function getId()
    {
        return 1;
    }
}