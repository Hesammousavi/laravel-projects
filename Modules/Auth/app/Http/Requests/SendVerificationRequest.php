<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Services\VerificationCodeService;

class SendVerificationRequest extends FormRequest
{
    public ContactType $contactType;
    public VerificationActionType $action;


    public function prepareForValidation()
    {
        $this->contactType = ContactType::detectContactType($this->input('contact') ?? '');
        $this->action = VerificationActionType::tryFrom($this->input('action') ?? '');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => [
                'bail',
                'required',
                'string',
                new Enum(VerificationActionType::class),
            ],
            'contact' => [
                'bail',
                'required',
                'string',
                ...$this->getContactValidationRules(),
            ]
        ];
    }

    public function after() : array
    {
        return [
            function(Validator $validator) {
                if($validator->errors()->isNotEmpty()) {
                    return;
                }

                $contact = $this->input('contact');
                $action = $this->action;
                $contactType = $this->contactType;

                $retryTime = (new VerificationCodeService)->getRetryTime($contact, $action, $contactType);

                if($retryTime) {
                    $validator->errors()->add('contact', __('auth::validation.contact_retry_time', ['retry_time' => abs(round($retryTime))]));
                }
            }
        ];
    }

    public function getContactValidationRules(): array
    {

        $verificaitonAction = $this->action;

        if(!$verificaitonAction) {
            return [];
        }

        if($this->contactType === ContactType::EMAIL) {
            // email validation
            return [
                'email:rfc,dns',
                Rule::when($verificaitonAction->isContactNeedToBeUnique(), [
                    'unique:users,email',
                ]),
                Rule::when($verificaitonAction->isContactNeedToBeExsit(), [
                    'exists:users,email',
                ]),
            ];
        }

        return [
            'phone:mobile',
            Rule::when($verificaitonAction->isContactNeedToBeUnique(), [
                'unique:users,phone',
            ]),
            Rule::when($verificaitonAction->isContactNeedToBeExsit(), [
                'exists:users,phone',
            ]),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
