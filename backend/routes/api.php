<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/registration', [UserController::class, 'registration']);
Route::get('/user/email/verify/{userId}', [UserController::class, 'verifyRegistration']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/password/reset', [UserController::class, 'resetPassword']);
Route::get('/user/password/reset/verify/{passwordResetVerifyToken}', [UserController::class, 'verifyPasswordResetToken']);