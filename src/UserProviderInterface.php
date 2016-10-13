<?php

namespace Nutrition;

interface UserProviderInterface
{
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
     * Check user is valid/loaded
     *
     * @return boolean
     */
    public function valid();

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
     * @return object $this
     */
    public function loadUser($value);

    /**
     * Load User From Session
     *
     * @return object $this
     */
    public function reload();

    /**
     * Get user data as array to save in session
     *
     * @return array
     */
    public function cast();

    /**
     * Copy from global var
     *
     * @param  string|array $var if string it should be global var key
     * @param  callback $func callback that should return array
     * @return object $this
     */
    public function copyfrom($var,$func=NULL);

    /**
     * Update user
     *
     * @return boolean
     */
    public function update();

    /**
     * Update password
     *
     * @param  string $plainPassword
     * @return object $this
     */
    public function updatePassword($plainPassword);
}
