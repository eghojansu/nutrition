<?php

namespace Nutrition\Security;

interface UserInterface
{
    /**
     * Get username
     * @return string
     */
    public function getUsername();

    /**
     * Get password
     * @return string
     */
    public function getPassword();

    /**
     * Is user expired
     * @return string
     */
    public function isExpired();

    /**
     * Is user block
     * @return string
     */
    public function isBlocked();

    /**
     * Get user roles
     * @return array
     */
    public function getRoles();
}
