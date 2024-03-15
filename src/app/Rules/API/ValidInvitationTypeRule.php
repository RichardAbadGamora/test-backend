<?php

namespace App\Rules\API;

use App\Enums\InvitationType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidInvitationTypeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedTypes = flatten_enum(InvitationType::class);
        $types = explode(',', $value);

        $result = array_diff($types, $allowedTypes);

        if (!empty($result)) {
            $fail(__('messages.invalid-invitation-type'));
        }
    }
}
