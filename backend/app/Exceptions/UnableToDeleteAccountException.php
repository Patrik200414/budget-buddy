<?php

namespace App\Exceptions;

use Exception;

class UnableToDeleteAccountException extends Exception
{
    public function __construct(string $message = "Unable to delete holdings account! You need an account to transfer the balance to.", int $code = 409, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
