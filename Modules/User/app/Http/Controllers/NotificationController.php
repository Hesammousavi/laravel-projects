<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\User;
use Modules\User\Transformers\NotificationResource;

class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        /** @var User */
        $user = $request->user();

        return NotificationResource::collection($user->unreadNotifications);
    }

    public function read(Request $request)
    {
        /** @var User */
        $user = $request->user();

        return NotificationResource::collection($user->readNotifications);
    }
}
