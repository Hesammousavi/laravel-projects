<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\Base\BaseAuthRequest;
use Modules\Auth\Services\VerificationCodeService;
use Modules\User\Models\User;

class ForgotPasswordRequest extends BaseAuthRequest
{

    public function prepareForValidation()
    {
        $this->prepareContactType();
        $this->action = VerificationActionType::FORGOT_PASSWORD;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'contact' => [
                'required',
                'string',
                ...$this->getContactValidationRules(),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255'
            ],
            'token' => [
                'required',
                'string',
                'max:255'
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

                $validatedData = $this->validated();

                if(!$this->validateVerificationToken($validator, $validatedData, $this->action, $validatedData['contact'])) {
                    return;
                }

                $this->validateAndAuthenticateUser($validator , $validatedData);
            }
        ];
    }
}
