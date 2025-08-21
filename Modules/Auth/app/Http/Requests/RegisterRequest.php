<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Services\VerificationCodeService;

class RegisterRequest extends FormRequest
{
    public ContactType $contactType;
    public string $contact;

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

                $tokenData = (new VerificationCodeService)->getVerificationToken(
                    $validatedData['token'],
                    [
                        'email' => $validatedData['email'],
                        'phone' => $validatedData['phone'],
                    ],
                    VerificationActionType::REGISTER,
                );

                if(!$tokenData) {
                    $validator->errors()->add('token', __('auth::validation.invalid_verification_token'));
                    return;
                }

                $this->contactType = $tokenData['contact_type'];
                $this->contact = $tokenData['contact'];
            }
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
