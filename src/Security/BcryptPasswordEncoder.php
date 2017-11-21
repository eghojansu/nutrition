<?php

namespace Nutrition\Security;

use Prefab;

/**
 * Crypt using Bcrypt
 */
class BcryptPasswordEncoder extends Prefab implements PasswordEncoderInterface
{
    /**
     * {@inheritdoc}
    */
    public function encodePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * {@inheritdoc}
    */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
