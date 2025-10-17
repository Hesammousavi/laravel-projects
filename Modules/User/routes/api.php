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

Route::get('test', function() {
    User::chunk(2 , function($users , $index) {
        foreach ($users as $user) {
            # code...
            $user->notify((new SendSpecialDiscountToUserNotificaiton())->onQueue('notificaiton')->delay(now()->addSeconds($index * 1)));
        }
    });





    // $service->update(
    //     $user,
    //     'welcome_message',
    //     'telegram',
    //     true
    // );


    return response()->json([
        'message' => 'send notification was successful'
    ]);
});


Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('auth/me' , [UserController::class , 'me'])
        ->name('me');


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
