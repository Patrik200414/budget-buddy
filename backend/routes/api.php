<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/registration', [UserController::class, 'registration']);
Route::get('/user/email/verify/{userId}', [UserController::class, 'verifyRegistration']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/password/reset', [UserController::class, 'resetPasswordRequest']);
Route::get('/user/password/reset/verify/{passwordResetVerifyToken}', [UserController::class, 'verifyPasswordResetToken']);
Route::put('/user/password/reset/{resetPasswordToken}', [UserController::class, 'resetPassword']);

Route::group(['middleware'=>'auth:sanctum'], function(){
    Route::put('/user/update', [UserController::class, 'updateUser']);
    Route::get('/user', [UserController::class, 'getUserInformation']);
});