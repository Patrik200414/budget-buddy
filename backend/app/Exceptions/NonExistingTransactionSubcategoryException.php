<?php

namespace App\Exceptions;

use Exception;

class NonExistingTransactionSubcategoryException extends Exception
{
    public function __construct(string $message = "The searched transaction subcategory doesn't exists!", int $code = 404, \Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
