<?php

namespace Modules\Auth\Http\Requests\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Modules\User\Models\User;

trait HasUserAuthentication
{
    public function validateAndAuthenticateUser(Validator $validator, array $validatedData) : bool
    {

        $user = User::where($this->contactType->value , $validatedData['contact'])->first();

        if(!$user) {
            $validator->errors()->add('contact', __('auth::validation.user_not_found'));
            return false;
        }

        $user->verifiedContact($this->contactType);
        Auth::onceUsingId($user->id);

        return true;
    }

    public function validatePasswordLogin(Validator $validator, array $validatedData)
    {
        if(empty($validatedData['password'])) {
            $validator->errors()->add('password', __('auth::validation.password_required'));
            return;
        }

        $credentials = [
            // email or phone => value
            $this->contactType->value => $validatedData['contact'],
            'password' => $validatedData['password'],
        ];

        if(!Auth::once($credentials)) {
            $validator->errors()->add('contact', __('auth::validation.invalid_credentials'));
            return;
        }
    }

    public function validateTokenLogin(Validator $validator, array $validatedData)
    {
        if(empty($validatedData['token'])) {
            $validator->errors()->add('token', __('auth::validation.token_required'));
            return;
        }

        if(!$this->validateVerificationToken($validator, $validatedData, $this->action, $validatedData['contact'])) {
            return;
        }

        $this->validateAndAuthenticateUser($validator , $validatedData);
    }
}
