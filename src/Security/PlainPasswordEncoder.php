<?php

namespace Nutrition\Security;

use Prefab;

/**
 * Crypt using Bcrypt
 */
class PlainPasswordEncoder extends Prefab implements PasswordEncoderInterface
{
    /**
     * {@inheritdoc}
    */
    public function encodePassword($password)
    {
        return $password;
    }

    /**
     * {@inheritdoc}
    */
    public function verifyPassword($password, $hash)
    {
        return strcmp($password, $hash) === 0;
    }
}
