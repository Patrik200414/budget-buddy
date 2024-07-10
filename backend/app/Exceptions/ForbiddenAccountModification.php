<?php

namespace App\Exceptions;

use Exception;

class ForbiddenAccountModification extends Exception
{
    public function __construct($message = "Forbidden modification! You don't have the permission to modify this account!", $code = 403, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
