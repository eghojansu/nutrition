<?php

namespace Nutrition\Security;

use Bcrypt;

class BcryptPassword implements PasswordEncoderInterface
{
    /**
     * Verify plain is equals with hash
     * @param  string $plain
     * @param  string $hash
     */
    public function verify($plain, $hash)
    {
        return Bcrypt::instance()->verify($plain, $hash);
    }

    /**
     * Hash plain text
     * @param  string $plain
     * @return string
     */
    public function encode($plain)
    {
        return Bcrypt::instance()->hash($plain);
    }
}