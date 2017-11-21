<?php

namespace Nutrition\Security;

interface PasswordEncoderInterface
{
    /**
     * Encode password
     * @param  string $password
     * @return string
     */
    public function encodePassword($password);

    /**
     * Verify password against a hash
     * @param  string $password
     * @param  string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash);
}
