<?php

namespace App\Http\Requests\API;

use App\Enums\OtpAuthAction;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $allowedActions = flatten_enum(OtpAuthAction::class);

        $rules = [
            'action' => 'required|string|in:' . implode(',', $allowedActions)
        ];

        if ($this->action === OtpAuthAction::EMAIL_PASS_LOGIN) {
            return array_merge($rules, [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);
        }

        $rules = array_merge($rules, [
            'otp_type' => 'required|string',
            'otp_value' => 'required|numeric',
            'context' => 'required|string'
        ]);

        if ($this->action === OtpAuthAction::EMAIL_PASS_REGISTRATION) {
            $rules = array_merge(unset_keys($rules, ['context']), [
                'context' => 'required|email',
                'password' => 'required|string',
                'firstname' => 'required|string',
                'lastname' => 'required|string'
            ]);
        }

        if ($this->action === OtpAuthAction::EMAIL_ONLY_LOGIN) {
            $rules['context'] = 'required|email';
        }

        return $rules;
    }
}
