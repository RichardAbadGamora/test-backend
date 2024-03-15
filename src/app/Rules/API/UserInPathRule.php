<?php

namespace App\Rules\API;

use App\Models\Path;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserInPathRule implements ValidationRule
{
    protected $path_id;

    public function __construct($path_id)
    {
        $this->path_id = $path_id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $user_id, Closure $fail): void
    {
        $path = Path::find($this->path_id);

        $exists = $path->users()->where(compact('user_id'))->exists();

        if ($exists) {
            $fail(__('messages.user-already-part-of-the-path'));
        }
    }
}
