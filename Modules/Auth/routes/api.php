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


    Route::post('code-verification/send-code', [VerificationController::class, 'sendCode'])
            ->name('code-verification.send-code')
            ->middleware('throttle:verification-code');
});
