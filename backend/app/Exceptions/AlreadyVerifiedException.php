<?php

namespace App\Exceptions;

use Exception;

class AlreadyVerifiedException extends Exception
{
    public function __construct($message = "Already verified account!", $code = 409, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
