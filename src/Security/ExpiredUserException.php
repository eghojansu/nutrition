<?php

namespace Nutrition\Security;

use Exception;

class ExpiredUserException extends Exception
{
    public function __construct($message = "Your account was expired", $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
