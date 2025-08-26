<?php

namespace Modules\User\Models;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($token) {
            $token->identifier = Auth::user()->identifierFromRequest();
        });
    }
}
