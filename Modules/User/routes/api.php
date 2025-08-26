<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\EnsureUserVerifiedMiddleware;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('auth/me' , [UserController::class , 'me'])
        ->name('me');
});
