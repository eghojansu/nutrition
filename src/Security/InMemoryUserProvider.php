<?php

namespace Nutrition\Security;

use Base;

class InMemoryUserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
    */
    public function loadByUsername($username)
    {
        $users = Base::instance()->get('SECURITY.users') ?: [];

        foreach ($users as $name => $value) {
            if ($name === $username) {
                return Security::instance()->getUserClass(true, $value);
            }
        }

        return null;
    }
}
