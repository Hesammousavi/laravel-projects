<?php

namespace Modules\Auth\Enums;

enum VerificationActionType : string {
    case REGISTER = 'register';
    case LOGIN = 'login';
    case FORGOT_PASSWORD = 'forgot_password';
    case VERIFY = 'verify';
    case CHANGE_INFO = 'change_info';


    public function isContactNeedToBeUnique(): bool
    {
        return in_array($this, [self::REGISTER, self::CHANGE_INFO]);
    }

    public function isContactNeedToBeExsit(): bool
    {
        return in_array($this, haystack: [self::LOGIN, self::FORGOT_PASSWORD, self::VERIFY]);
    }
}
