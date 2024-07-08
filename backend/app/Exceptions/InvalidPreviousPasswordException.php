<?php

namespace App\Exceptions;

use Exception;

class InvalidPreviousPasswordException extends Exception
{
    public function __construct(string $message = "Invalid previous password!", int $code = 409, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
