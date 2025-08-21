<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Enums\ContactType;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\User\Models\User;

class RegisterUser
{
    public function handle(RegisterRequest $request) : User {
        $userData = $request->validated();

        $user = User::create($userData);
        $user->verifiedContact($request->contactType);

        return $user;
    }
}
