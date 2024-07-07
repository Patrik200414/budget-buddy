<?php

namespace App\Exceptions;

use Exception;

class NotRequestedPasswordChange extends Exception
{
    public function __construct(string $message = "The password reset wasn't requested! Couldn't find which user would like to change password!", int $code = 404, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
