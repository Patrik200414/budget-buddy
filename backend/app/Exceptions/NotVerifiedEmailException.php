<?php

namespace App\Exceptions;

use Exception;

class NotVerifiedEmailException extends Exception
{
    public function __construct(string $message = "The given email isn't verified! Please check your email!", int $code = 403, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
