<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Services\VerificationCodeService;
use Modules\User\Models\User;

class ForgotPasswordRequest extends FormRequest
{
    public ContactType $contactType;


    public function prepareForValidation()
    {
        $this->contactType = ContactType::detectContactType($this->input('contact') ?? '');
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

                $tokenData = (new VerificationCodeService)->getVerificationToken(
                    $validatedData['token'],
                    [
                        'email' => $validatedData['contact'],
                        'phone' => $validatedData['contact'],
                    ],
                    VerificationActionType::FORGOT_PASSWORD,
                    hash('sha256', $this->userAgent() . ':' . $this->ip()),
                );

                if(!$tokenData) {
                    $validator->errors()->add('token', __('auth::validation.invalid_verification_token'));
                    return;
                }

                $user = User::where($this->contactType->value , $validatedData['contact'])->first();

                if(!$user) {
                    $validator->errors()->add('contact', __('auth::validation.user_not_found'));
                    return;
                }

                $user->verifiedContact($this->contactType);
                Auth::onceUsingId($user->id);
            }
        ];
    }

    public function getContactValidationRules(): array
    {
        if($this->contactType === ContactType::EMAIL) {
            // email validation
            return [
                'email',
                'exists:users,email'
            ];
        }

        return [
            'phone:mobile',
            'exists:users,phone',
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
