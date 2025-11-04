<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\EnsureUserVerifiedMiddleware;
use Modules\Auth\Notifications\WelcomeMessage;
use Modules\User\Http\Controllers\NotificationController;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\UserNotificationPreferenceController;
use Modules\User\Models\User;
use Modules\User\Notifications\PaymentPaidNotification;
use Modules\User\Notifications\SendSpecialDiscountToUserNotificaiton;
use Modules\User\Services\NotificationPreferenceService;


Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('auth/me' , [UserController::class , 'me'])
        ->name('me');

    Route::middleware('throttle:upload-file')->post('/cover/upload' , [UserController::class , 'coverUpload']);

    Route::prefix('profile')->group(function() {
        Route::get('notifications/unread' , [NotificationController::class , 'unread'])
        ->name('notifications.unread');
        Route::get('notifications/read' , [NotificationController::class , 'read'])
        ->name('notifications.read');

        Route::get('notifications/preferences' , [UserNotificationPreferenceController::class , 'index'])
        ->name('notifications.perferences');
        Route::patch('notifications/preferences' , [UserNotificationPreferenceController::class , 'update']);
    });
});
