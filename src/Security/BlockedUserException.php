<?php

namespace Nutrition\Security;

use Exception;

class BlockedUserException extends Exception
{
    public function __construct($message = "Your account was blocked", $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
