<?php

namespace App\Rules\API;

use Closure;
use Dotenv\Exception\ValidationException;
use Illuminate\Contracts\Validation\ValidationRule;

class InvitedEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $invitation = request('invitation');

        if ($invitation->invitee_email !== request('email')) {
            $fail(__('messages.email-not-same-as-invited-email'));
        }
    }
}
