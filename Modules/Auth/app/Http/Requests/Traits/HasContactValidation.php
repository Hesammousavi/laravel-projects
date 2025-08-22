<?php

namespace Modules\Auth\Http\Requests\Traits;

use Modules\Auth\Enums\ContactType;

trait HasContactValidation
{
    public function getContactValidationRules(): array
    {
        if($this->contactType === ContactType::EMAIL) {
            // email validation
            return $this->getEmailValidationRules();
        }

        return $this->getPhoneValidationRules();
    }

    public function getEmailValidationRules(): array
    {
        $rules = ['email:rfc,dns'];

        if ($this->action) {
            if ($this->action->isContactNeedToBeUnique()) {
                $rules[] = 'unique:users,email';
            }
            if ($this->action->isContactNeedToBeExsit()) {
                $rules[] = 'exists:users,email';
            }
        }

        return $rules;
    }


        /**
     * Get phone validation rules
     */
    protected function getPhoneValidationRules(): array
    {
        $rules = ['phone:mobile'];

        if ($this->action) {
            if ($this->action->isContactNeedToBeUnique()) {
                $rules[] = 'unique:users,phone';
            }
            if ($this->action->isContactNeedToBeExsit()) {
                $rules[] = 'exists:users,phone';
            }
        }

        return $rules;
    }

}
