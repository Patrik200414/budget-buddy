<?php

namespace App\Exceptions;

use Exception;

class NonExistingAccount extends Exception
{
    public function __construct(string $message = "Non existing account!", int $code = 404, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
