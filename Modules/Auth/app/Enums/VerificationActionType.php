<?php

namespace Modules\Auth\Enums;

enum VerificationActionType : string {
    case REGISTER = 'register';
    case LOGIN = 'login';
    case FORGOT_PASSWORD = 'forgot_password';


    public function isContactNeedToBeUnique(): bool
    {
        return in_array($this, [self::REGISTER]);
    }

    public function isContactNeedToBeExsit(): bool
    {
        return in_array($this, haystack: [self::LOGIN, self::FORGOT_PASSWORD]);
    }
}
