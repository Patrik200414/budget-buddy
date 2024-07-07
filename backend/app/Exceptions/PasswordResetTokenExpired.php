<?php

namespace App\Exceptions;

use Exception;

class PasswordResetTokenExpired extends Exception
{
    public function __construct(string $message = "The password reset token is expired! If you still want to change password please click on 'Forget password' on the login page!", int $code = 401, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
