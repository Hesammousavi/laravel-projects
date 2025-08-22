<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\Base\BaseAuthRequest;
use Modules\Auth\Services\VerificationCodeService;
use Modules\User\Models\User;

class LoginRequest extends BaseAuthRequest
{
    public function prepareForValidation()
    {
        $this->prepareContactType();
        $this->action = VerificationActionType::LOGIN;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'login_type' => [
                'required',
                'string',
                'in:password,token',
            ],
            'contact' => [
                'required',
                'string',
                ...$this->getContactValidationRules(),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
            ],
            'token' => [
                'nullable',
                'string',
                'max:255',
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

                if($this->input('login_type') === 'password') {
                    $this->validatePasswordLogin($validator,$validatedData);
                } else {
                    $this->validateTokenLogin($validator,$validatedData);
                }
            }
        ];
    }
}
