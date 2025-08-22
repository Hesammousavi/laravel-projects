<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Http\Requests\Base\BaseAuthRequest;

class CheckUserRequest extends BaseAuthRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'contact' => [
                'bail',
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $validator = validator(['email' => $value], ['email' => 'email:rfc,dns']);
                        if ($validator->fails()) {
                            $fail(__('auth::validation.contact_invalid_email'));
                        }

                        return;
                    }

                    $validator = validator(['phone' => $value], ['phone' => 'phone:mobile']);
                    if ($validator->fails()) {
                        $fail(__('auth::validation.contact_invalid_phone'));
                    }

                    return;
                },
            ]
        ];
    }
}
