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
     * @return Object $this
     */
    public function loadUser($id);

    /**
     * Get user data as array to save in session
     *
     * @return array
     */
    public function cast();
}
