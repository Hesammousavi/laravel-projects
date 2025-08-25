<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Auth\Emails\VerificationCodeEmail;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\SendVerificationRequest;
use Modules\Auth\Http\Requests\VerifyVerificationRequest;
use Modules\Base\Services\SmsService;

class VerificationCodeService
{
    private const TOKEN_LENGTH = 100;
    private const TOKEN_EXPIRY_MINUTES = 10;
    private const CODE_MIN = 100000;
    private const CODE_MAX = 999999;
    private const EMAIL_EXPIRY_MINUTES = 5;
    private const SMS_EXPIRY_MINUTES = 1;
    private TokenStorageService $tokenService;
    private CodeStorageService $codeService;
    private SmsService $smsService;

    public function __construct()
    {
        $this->tokenService = new TokenStorageService();
        $this->codeService = new CodeStorageService();
        $this->smsService = new SmsService();
    }

    public function createVerificationToken(VerifyVerificationRequest $request)
    {
        do {
            $token = Str::random(self::TOKEN_LENGTH);
        } while ($this->tokenService->has($token));

        // check if token is already used
        $this->tokenService->save($token , $request , self::TOKEN_EXPIRY_MINUTES);

        return $token;
    }

    public function getVerificationToken(string $token , array $contactList, VerificationActionType $action, string $identifier) : ?array
    {
        $tokenData = $this->tokenService->get($token);

        if(!$tokenData) {
            return null;
        }

        $contact = $tokenData['contact_type'] === ContactType::EMAIL ? $contactList['email'] : $contactList['phone'];

        if(
            $tokenData['contact'] === $contact &&
            $tokenData['action'] === $action &&
            $tokenData['identifier'] === $identifier
        ) {
            return $tokenData;
        }


        return null;
    }


    public function generateCode(string $contact , VerificationActionType $action , ContactType $contactType, ?int $expiryMinutes = null)
    {
        $expiryMinutes = $expiryMinutes ?? $this->getDefaultExpiryMinutes($contactType);
        $expiredAt = now()->addMinutes($expiryMinutes);

        $code = random_int(self::CODE_MIN, self::CODE_MAX);

        $this->codeService->save(
            $contact,
            $action,
            $contactType,
            $code,
            $expiredAt
        );

        return $code;
    }

    public function getRetryTime(string $contact , VerificationActionType $action , ContactType $contactType) : ?int
    {
        $codeData = $this->codeService->get($contact, $action, $contactType);

        return ( $codeData && now()->isAfter( $codeData['expired_at']) )
            ? $codeData['expired_at']->diffInSeconds(now())
            : null;
    }


    public function forgetCode(string $contact , VerificationActionType $action , ContactType $contactType)
    {
        $this->codeService->forget($contact, $action, $contactType);
    }

    public function sendCodeAsSms(SendVerificationRequest $request, string $contact , int $code) : bool
    {
        $success = $this->smsService->sendVerificationCode($contact, $code);

        if (!$success) {
            $this->codeService->forget($contact, $request->action , $request->contactType);
        }

        return $success;
    }

    public function sendCodeAsMail(SendVerificationRequest $request, string $contact , int $code) : bool
    {
        try {
            Mail::to($contact)->send(new VerificationCodeEmail($code));

            return true;
        } catch (\Throwable $th) {
            //throw $th;
            $this->codeService->forget($contact, $request->action , $request->contactType);

            return false;
        }
    }

    public function verifyCode(string $contact , VerificationActionType $action , ContactType $contactType , string $code) : bool
    {
        $cacheData = $this->codeService->get($contact, $action, $contactType);

        if(
            $cacheData &&
            now()->isAfter($cacheData['expired_at']) &&
            (string) $cacheData['code'] === $code
        ) {
            $this->codeService->forget($contact, $action, $contactType);

            return true;
        }

        return false;
    }

    private function getDefaultExpiryMinutes(ContactType $contactType) : int
    {
        return match($contactType) {
            ContactType::EMAIL => self::EMAIL_EXPIRY_MINUTES,
            ContactType::PHONE => self::SMS_EXPIRY_MINUTES,
        };
    }
}
