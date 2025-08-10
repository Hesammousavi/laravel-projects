<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Services\VerificationCodeService;

class VerifyVerificationRequest extends FormRequest
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
            ],
            'code' => [
                'bail',
                'required',
                'string',
                'digits:6',
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

                if(!
                    (new VerificationCodeService)->verifyCode(
                        $contact,
                         $action,
                         $contactType,
                        $this->input('code')
                    )
                )
                     $validator->errors()->add('code', __('auth::validation.invalid_verification_code'));

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
                ])
            ];
        }

        return [
            'phone:mobile',
            Rule::when($verificaitonAction->isContactNeedToBeUnique(), [
                'unique:users,phone',
            ])
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
