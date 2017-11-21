<?php

namespace Nutrition\Security;

interface UserProviderInterface
{
    /**
     * Load user from database
     * @param  string $username
     * @return UserInterface|null
     */
    public function loadByUsername($username);
}
