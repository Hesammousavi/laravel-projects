<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;

class VerificationCodeService
{

    public function getCacheKey(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        $contact = hash('sha256', "{$action->value}:{$contactType->value}:{$contact}");
        return "verification:$contact";
    }

    public function generateCode(string $contact , VerificationActionType $action , ContactType $contactType, ?int $expiryMinutes = null)
    {
        if($expiryMinutes === null) {
            $expiryMinutes = $contactType === ContactType::EMAIL ? 5 : 1;
        }

        $code = random_int(100000, 999999);

        $cacheKey = $this->getCacheKey($contact, $action, $contactType);
        $expiredAt = now()->addMinutes($expiryMinutes);

        Cache::put($cacheKey, [
            'code' => $code,
            'expired_at' => $expiredAt,
        ], $expiredAt);

        return $code;
    }

    public function getRetryTime(string $contact , VerificationActionType $action , ContactType $contactType) : ?int
    {
        $cacheKey = $this->getCacheKey($contact, $action, $contactType);
        $cacheValue = Cache::get($cacheKey);

        return $cacheValue && now()->diffInSeconds( $cacheValue['expired_at']) > 0 ? $cacheValue['expired_at']->diffInSeconds(now()) : null;
    }

    public function sendCode()
    {}

    public function handle() {}
}
