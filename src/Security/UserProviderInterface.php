<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Security;

interface UserProviderInterface
{
    /**
     * Authenticate with username
     * @param  string $username of course, it can be email too
     * @return bool
     */
    public function authenticate($username);

    /**
     * Load user data
     * @param  mixed $id user_id
     */
    public function loadUserData($id);

    /**
     * Get session id
     * @return string
     */
    public function getSessionID();

    /**
     * Get password
     * @return  string
     */
    public function getPassword();

    /**
     * Get user id
     * @return  int
     */
    public function getId();
}