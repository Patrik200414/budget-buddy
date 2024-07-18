<?php
namespace App\Handlers;
abstract class Handler{
    protected $nextHandler;

    public function setNext(Handler $handler): Handler{
        $this->nextHandler = $handler;
        return $handler;
    }
    
    abstract public function handle(mixed &$request);
}