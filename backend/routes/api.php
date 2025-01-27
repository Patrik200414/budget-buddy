<?php

use App\Http\Controllers\BaseAccountController;
use App\Http\Controllers\SavingsAccountController;
use App\Http\Controllers\UserController;
use App\Http\Requests\SavingsAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$savingsAccountController = new SavingsAccountController();

Route::post('/user/registration', [UserController::class, 'registration']);
Route::get('/user/email/verify/{userId}', [UserController::class, 'verifyRegistration']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/password/reset', [UserController::class, 'resetPasswordRequest']);
Route::get('/user/password/reset/verify/{passwordResetVerifyToken}', [UserController::class, 'verifyPasswordResetToken']);
Route::put('/user/password/reset/{resetPasswordToken}', [UserController::class, 'resetPassword']);

Route::group(['middleware'=>'auth:sanctum'], function() use($savingsAccountController){
    Route::put('/user/update', [UserController::class, 'updateUser']);
    Route::get('/user', [UserController::class, 'getUserInformation']);

    Route::post('/account/savings', function(SavingsAccountRequest $request) use($savingsAccountController){
        return $savingsAccountController->createAccount($request);
    });

    Route::put('/account/savings/{accountId}', function(SavingsAccountRequest $request, string $accountId) use($savingsAccountController){
        return $savingsAccountController->updateAccount($request, $accountId);
    });

    Route::get('/account/savings/{accountId}', [SavingsAccountController::class, 'getAccount']);

    Route::delete('/account', [BaseAccountController::class, 'deleteAccount']);
    

    Route::patch('/account/savings/block/{accountId}', [BaseAccountController::class, 'blockAccount']);

    Route::get('/account/{accountId}/summary', [BaseAccountController::class, 'getAccountSummary']);

    Route::get('/account/summary', [BaseAccountController::class, 'getAccountSummariesByUser']);

    Route::get('/account/transfer/{transferFromAccountId}', [BaseAccountController::class, 'getTransferToAccounts']);
});