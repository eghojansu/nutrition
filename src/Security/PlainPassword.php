<?php

namespace Nutrition\Security;

class PlainPassword implements PasswordEncoderInterface
{
    /**
     * Verify plain is equals with hash
     * @param  string $plain
     * @param  string $hash
     */
    public function verify($plain, $hash)
    {
        return strcmp($plain, $hash)===0;
    }

    /**
     * Hash plain text
     * @param  string $plain
     * @return string
     */
    public function encode($plain)
    {
        return $plain;
    }
}