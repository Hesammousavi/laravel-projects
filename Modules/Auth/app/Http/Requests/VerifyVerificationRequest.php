<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\Base\BaseAuthRequest;

class VerifyVerificationRequest extends BaseAuthRequest
{
    public string $identifier;

    public function prepareForValidation()
    {
        $this->prepareContactType();
        $this->prepareAction();
        $this->identifier = hash('sha256', $this->userAgent() . ':' . $this->ip());
    }

    public function authorize(): bool
    {
        if($this->action === VerificationActionType::CHANGE_INFO && !Auth::check()) {
            return false;
        }

        return true;
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

                $this->validateVerificationCode($validator, $this->validated(), $this->action , $this->contactType);
            }
        ];
    }
}
