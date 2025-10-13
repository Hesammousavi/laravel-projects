<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\EnsureUserVerifiedMiddleware;
use Modules\Auth\Notifications\WelcomeMessage;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\UserNotificationPreferenceController;
use Modules\User\Models\User;
use Modules\User\Services\NotificationPreferenceService;

Route::get('test', function() {
    $user = User::find(4);
    $user->notify((new WelcomeMessage()));

    $service = new NotificationPreferenceService();
    dd($service->allowedChannels($user , 'welcome_message' , []));

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
        Route::get('notifications/preferences' , [UserNotificationPreferenceController::class , 'index'])
        ->name('notifications_perferences');
        Route::patch('notifications/preferences' , [UserNotificationPreferenceController::class , 'update']);
    });
});
