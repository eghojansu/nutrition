<?php

namespace Nutrition\Security;

use Nutrition\MagicService;
use Base;

/**
 * Sample user class
 */
class User implements UserInterface
{
    protected $Username;
    protected $Password;
    protected $Roles;
    protected $Expired;
    protected $Blocked;


    public function __construct($username, $password, array $roles, $expired = false, $blocked = false)
    {
        $this->Username = $username;
        $this->Password = $password;
        $this->Roles = $roles;
        $this->Expired = $expired;
        $this->Blocked = $blocked;
    }

    /**
     * {@inheritdoc}
    */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * {@inheritdoc}
    */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * {@inheritdoc}
    */
    public function getRoles()
    {
        return $this->Roles;
    }

    /**
     * {@inheritdoc}
    */
    public function isExpired()
    {
        return $this->Expired;
    }

    /**
     * {@inheritdoc}
    */
    public function isBlocked()
    {
        return $this->Blocked;
    }
}
