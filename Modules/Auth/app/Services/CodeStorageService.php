<?php

namespace Modules\Auth\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\VerifyVerificationRequest;

class CodeStorageService
{
    private const VERIFICATION_CACHE_PREFIX = "verificaiton:";

    public function getKey(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        $contact = hash('sha256', "{$action->value}:{$contactType->value}:{$contact}");

        return self::VERIFICATION_CACHE_PREFIX . $contact;
    }

    public function save(string $contact , VerificationActionType $action , ContactType $contactType , int $code , Carbon $expiredAt): void
    {
        Cache::put(key: $this->getKey($contact, $action, $contactType), value: [
            'code' => $code,
            'expired_at' => $expiredAt,
            'user_id' => Auth::id(),
        ], ttl: $expiredAt);
    }

    public function get(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        return Cache::get($this->getKey($contact, $action, $contactType));
    }

    public function forget(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        Cache::forget($this->getKey($contact, $action, $contactType));
    }
}
