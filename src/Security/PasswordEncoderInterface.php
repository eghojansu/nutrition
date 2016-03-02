<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Security;

interface PasswordEncoderInterface
{
    /**
     * Verify plain is equals with hash
     * @param  string $plain
     * @param  string $hash
     */
    public function verify($plain, $hash);

    /**
     * Hash plain text
     * @param  string $plain
     * @return string
     */
    public function encode($plain);
}