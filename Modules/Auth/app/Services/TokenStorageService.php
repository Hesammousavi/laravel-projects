<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Auth\Http\Requests\VerifyVerificationRequest;

class TokenStorageService
{
    private const TOKEN_CACHE_PREFIX = "verificaiton:after_verify:token:";

    public function getKey(string $token)
    {
        return self::TOKEN_CACHE_PREFIX . $token;
    }

    public function has(string $token)
    {
        return Cache::has($this->getKey($token));
    }

    public function save(string $token, VerifyVerificationRequest $request , int $expiryInMinutes): void
    {
        Cache::put($this->getKey($token), [
            'contact' => $request->input('contact'),
            'action' => $request->action,
            'contact_type' => $request->contactType,
            'identifier' => $request->identifier,
        ] , now()->addMinutes($expiryInMinutes));
    }

    public function get(string $token)
    {
        return Cache::pull($this->getKey($token));
    }
}
