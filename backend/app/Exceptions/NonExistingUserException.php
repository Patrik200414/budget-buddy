<?php

namespace App\Exceptions;

use Exception;

class NonExistingUserException extends Exception
{
    public function __construct($message = "Non existing registration!", $code = 404, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
