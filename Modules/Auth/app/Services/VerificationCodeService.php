<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Emails\VerificationCodeEmail;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\SendVerificationRequest;

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


    public function forgetCode(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        $cacheKey = $this->getCacheKey($contact, $action, $contactType);
        Cache::forget($cacheKey);
    }

    public function sendCodeAsSms(SendVerificationRequest $request, string $contact , int $code) : bool
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => config('auth.msgway.api_key'),
            ])->post('https://api.msgway.com/send', [
                'mobile' => $contact,
                'method' => 'sms',
                'templateID' => 3,
                'params' => [
                    (string) $code,
                ],
            ]);


            $response->throw();

            return $response->successful();
        } catch (\Throwable $th) {
            //throw $th;
            $this->forgetCode($contact, $request->action , $request->contactType);

            return false;
        }
    }

    public function sendCodeAsMail(SendVerificationRequest $request, string $contact , int $code) : bool
    {
        try {
            Mail::to($contact)->send(new VerificationCodeEmail($code));

            return true;
        } catch (\Throwable $th) {
            //throw $th;
            $this->forgetCode($contact, $request->action , $request->contactType);

            return false;
        }
    }

    public function verifyCode(string $contact , VerificationActionType $action , ContactType $contactType , string $code) : bool
    {
        $cacheKey = $this->getCacheKey($contact, $action, $contactType);
        $cacheValue = Cache::get($cacheKey);



        if( $cacheValue && now()->diffInSeconds($cacheValue['expired_at']) > 0 && (string) $cacheValue['code'] === $code) {
            $this->forgetCode($contact, $action, $contactType);
            return true;
        }

        return false;
    }
}
