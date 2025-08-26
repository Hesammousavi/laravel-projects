<?php

namespace Modules\Auth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Enums\ContactType;

class ContactBelongsToAuthenticatedUser implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $contactType = ContactType::detectContactType($value);

        $user = Auth::user();

        if(!$user || $user?->{$contactType->value} !== $value) {
            $fail(__('auth::verification.contact_not_belongs_to_authenticated_user'));
        }
    }
}
