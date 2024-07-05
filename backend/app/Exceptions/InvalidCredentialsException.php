<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct($message = "Invalid credentials!", $code = 401, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
