<?php

namespace Nutrition;

interface UserProviderInterface
{
    /**
     * Get user id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Get Plain password
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles();

    /**
     * Check user status
     *
     * @return boolean
     */
    public function isActive();

    /**
     * Check user is blocked
     *
     * @return boolean
     */
    public function isBlocked();

    /**
     * Validate password
     *
     * @param  string
     *
     * @return boolean
     */
    public function validatePassword($plainPassword);

    /**
     * Encrypt password
     *
     * @param  string $plainPassword plain
     * @return string
     */
    public function encryptPassword($plainPassword);

    /**
     * load user
     *
     * @param  string
     */
    public function loadUser($id);
}
