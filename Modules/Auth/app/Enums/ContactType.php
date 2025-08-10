<?php

namespace Modules\Auth\Enums;

enum ContactType : string {
    case EMAIL = 'email';
    case PHONE = 'phone';

    public static function detectContactType(string $contact) : self {
        if(filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            return self::EMAIL;
        }

        return self::PHONE;
    }
}
