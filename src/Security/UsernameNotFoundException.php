<?php

namespace Nutrition\Security;

use Exception;

class UsernameNotFoundException extends Exception
{
    public function __construct($message = "Invalid credentials", $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
