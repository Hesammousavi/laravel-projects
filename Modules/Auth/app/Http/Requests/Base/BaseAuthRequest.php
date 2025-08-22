<?php

namespace Modules\Auth\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Enums\ContactType;
use Modules\Auth\Enums\VerificationActionType;
use Modules\Auth\Http\Requests\Traits\HasContactValidation;
use Modules\Auth\Http\Requests\Traits\HasUserAuthentication;
use Modules\Auth\Http\Requests\Traits\HasVerificationValidation;

class BaseAuthRequest extends FormRequest
{
    use HasContactValidation , HasVerificationValidation , HasUserAuthentication;

    public ContactType $contactType;
    public ?VerificationActionType $action = null;


    public function prepareContactType() : void
    {
        $this->contactType =  ContactType::detectContactType($this->input('contact') ?? '');
    }


    public function prepareAction() : void
    {
        $this->action = VerificationActionType::tryFrom($this->input('action') ?? '');
    }

    /**
     * Determine if the user is authorized to make this request.
    */
    public function authorize(): bool
    {
        return true;
    }

}
