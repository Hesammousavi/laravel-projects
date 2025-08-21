<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Enums\ContactType;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\User\Models\User;

class RegisterUser
{
    public function handle(RegisterRequest $request) : User {
        $userData = $request->validated();

        if($request->contactType === ContactType::EMAIL) {
            $userData['email_verified_at'] = now();
        } else {
            $userData['phone_verified_at'] = now();
        }

        $user = User::create($userData);

        // must create token for user api

        return $user;
    }
}
