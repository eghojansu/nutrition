<?php

namespace Nutrition\Security;

use Nutrition\MagicService;
use Base;

/**
 * Sample user class
 */
class User implements UserInterface
{
    protected $username;
    protected $password;
    protected $roles;
    protected $expired;
    protected $blocked;


    public function __construct($username, $password, array $roles, $expired = false, $blocked = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->expired = $expired;
        $this->blocked = $blocked;
    }

    /**
     * {@inheritdoc}
    */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
    */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
    */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
    */
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * {@inheritdoc}
    */
    public function isBlocked()
    {
        return $this->blocked;
    }
}
