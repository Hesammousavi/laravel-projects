<?php

namespace Modules\Auth\Http\Requests\Traits;

use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Services\VerificationCodeService;


trait HasVerificationValidation
{
    public function validateVerificationToken(Validator $validator, array $validatedData, VerificationActionType $action, string|array $contact) : ?array
    {
        $tokenData = (new VerificationCodeService)->getVerificationToken(
            $validatedData['token'],
            [
                'email' => is_array($contact) ? $contact['email'] : $contact,
                'phone' => is_array($contact) ? $contact['phone'] : $contact,
            ],
            $action,
            hash('sha256', $this->userAgent() . ':' . $this->ip()),
        );

        if(!$tokenData) {
            $validator->errors()->add('token', __('auth::validation.invalid_verification_token'));
            return null;
        }

        return $tokenData;
    }


    public function validateVerificationCode(Validator $validator, array $validatedData, VerificationActionType $action , ContactType $contactType) : bool
    {
        if(! (new VerificationCodeService)->verifyCode( $validatedData['contact'], $action, $contactType, $validatedData['code'] )) {
            $validator->errors()->add('code', __('auth::validation.invalid_verification_code'));
            return false;
        }

        return true;
    }

    public function checkRetryTime(Validator $validator, array $validatedData, VerificationActionType $action , ContactType $contactType) : bool
    {
        $retryTime = (new VerificationCodeService)->getRetryTime($validatedData['contact'], $action, $contactType);

        if($retryTime) {
            $validator->errors()->add('contact', __('auth::validation.contact_retry_time', ['retry_time' => abs(round($retryTime))]));
            return false;
        }

        return true;
    }

}
