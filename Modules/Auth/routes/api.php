<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\VerficationController;
use Modules\Auth\Http\Controllers\VerificationController;

// /api/v1/auth/check-user
Route::middleware([])->prefix('v1/auth')->group(function () {
    Route::post('check-user', [AuthController::class, 'checkUser'])
        ->name('check-user')
        ->middleware('throttle:check-user');


    Route::post('code-verification/send', [VerificationController::class, 'sendCode'])
            ->name('code-verification.send-code')
            ->middleware('throttle:verification-code');

    Route::post('code-verification/verify', [VerificationController::class, 'verifyCode'])
            ->name('code-verification.verify')
            ->middleware('throttle:verification-code');


    Route::post('register' , [AuthController::class , 'register'])
        ->name('register')
        ->middleware('throttle:auth_user');

    Route::post('login' , [AuthController::class , 'login'])
        ->name('login')
        ->middleware('throttle:auth_user');

    Route::post('forgot_password' , [AuthController::class , 'forgotPassword'])
        ->name('forgot_password')
        ->middleware('throttle:auth_user');
});
