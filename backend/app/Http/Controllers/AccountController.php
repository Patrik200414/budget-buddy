<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use Illuminate\Http\Request;

abstract class AccountController extends Controller
{
    abstract public function createAccount(AccountRequest $request);
    abstract public function updateAccount(AccountRequest $request, string $accountId);
    abstract public function getAccount(string $accountId);
}
