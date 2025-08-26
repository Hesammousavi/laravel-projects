<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\VerficationController;
use Modules\Auth\Http\Controllers\VerificationController;
use Modules\Auth\Http\Middleware\EnsureUserVerifiedMiddleware;

Route::withoutMiddleware(EnsureUserVerifiedMiddleware::class)
->prefix('v1/auth')
->group(function () {

    Route::middleware(['throttle:verification-code'])
    ->prefix('code-verification')
    ->name('code-verification.')
    ->group(function () {
        Route::post('send', [VerificationController::class, 'sendCode'])
            ->name('send-code');

        Route::post('verify', [VerificationController::class, 'verifyCode'])
            ->name('verify');
    });

    Route::middleware(['guest' ,'throttle:auth_user'])->group(function () {
        Route::post('check-user', [AuthController::class, 'checkUser'])
            ->name('check-user')
            ->middleware('throttle:check-user');

        Route::post('register' , [AuthController::class , 'register'])
            ->name('register');

        Route::post('login' , [AuthController::class , 'login'])
            ->name('login');

        Route::post('forgot_password' , [AuthController::class , 'forgotPassword'])
            ->name('forgot_password');
    });
});
