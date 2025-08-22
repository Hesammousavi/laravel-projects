<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\Base\BaseAuthRequest;
use Modules\Auth\Services\VerificationCodeService;

class RegisterRequest extends BaseAuthRequest
{
    public string $contact;

    public function prepareForValidation()
    {
        $this->action = VerificationActionType::REGISTER;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
            ],
            'phone' => [
                'required',
                'string',
                'phone:mobile',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'token' => [
                'required',
                'string',
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

                $tokenData = $this->validateVerificationToken(
                    $validator,
                    $validatedData,
                    $this->action,
                    [
                        'email' => $validatedData['email'],
                        'phone' => $validatedData['phone']
                    ]
                );

                if(!$tokenData) {
                    return;
                }

                $this->contactType = $tokenData['contact_type'];
                $this->contact = $tokenData['contact'];
            }
        ];
    }
}
