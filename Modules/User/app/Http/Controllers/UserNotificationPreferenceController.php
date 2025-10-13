<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Http\Requests\UpdateNotificationPreferenceRequest;
use Modules\User\Models\User;
use Modules\User\Services\NotificationPreferenceService;
use Modules\User\Transformers\NotificationPreferenceResource;

class UserNotificationPreferenceController extends Controller
{
    public function __construct(protected NotificationPreferenceService $notificationPreferenceService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return new NotificationPreferenceResource(
            $this->notificationPreferenceService->getPerferences($request->user())
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationPreferenceRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $this->notificationPreferenceService->update(
            $user,
            $data['notification_type'],
            $data['channel'],
            $data['value']
        );

        return new NotificationPreferenceResource(
            $this->notificationPreferenceService->getPerferences($user)
        );
    }
}
