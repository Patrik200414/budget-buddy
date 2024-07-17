<?php
namespace App\Handlers;
abstract class Handler{
    abstract public function setNext(Handler $handler);
    abstract public function handle(mixed &$request);
}