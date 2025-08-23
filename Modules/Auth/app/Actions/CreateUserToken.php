<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Crypt;
use Modules\User\Models\User;

class CreateUserToken
{
    public function handle(User $user, $tokenName = 'x_web_token' , $expiresAt = 30, bool $isEncrypted = false) : string
    {
        $token = $user->createToken($tokenName , expiresAt: now()->addDays(value: $expiresAt))->plainTextToken;

        if($isEncrypted) {
            $token = Crypt::encryptString($token);
        }

        return $token;
    }
}
