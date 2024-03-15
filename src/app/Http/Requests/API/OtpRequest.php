<?php

namespace App\Http\Requests\API;

use App\Enums\OtpAuthAction;
use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
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
            'type' => 'required|string|in:' . implode(',', $allowedActions)
        ];

        if ($this->type === OtpAuthAction::PHONE_ONLY_LOGIN) {
            return array_merge($rules, [
                'phone' => 'required|string|exists:users,contact_no'
            ]);
        }

        if ($this->type === OtpAuthAction::EMAIL_ONLY_LOGIN) {
            return array_merge($rules, [
                'email' => 'required|email|exists:users,email'
            ]);
        }

        if ($this->type === OtpAuthAction::EMAIL_ONLY_REGISTRATION) {
            return array_merge($rules, [
                'email' => 'required|email|unique:users,email'
            ]);
        }

        if ($this->type === OtpAuthAction::PHONE_ONLY_REGISTRATION) {
            return array_merge($rules, [
                'phone' => 'required|string|unique:users,contact_no'
            ]);
        }
    }

    public function messages()
    {
        if ($this->type === OtpAuthAction::PHONE_ONLY_LOGIN) {
            return [
                'exists' => __('messages.phone-number-not-found')
            ];
        }

        if ($this->type === OtpAuthAction::EMAIL_ONLY_LOGIN) {
            return [
                'exists' => __('messages.email-not-found')
            ];
        }

        if ($this->type === OtpAuthAction::EMAIL_ONLY_REGISTRATION) {
            return [
                'unique' => __('messages.email-already-exists')
            ];
        }

        if ($this->type === OtpAuthAction::PHONE_ONLY_REGISTRATION) {
            return [
                'unique' => __('messages.phone-number-already-exists')
            ];
        }
    }
}
