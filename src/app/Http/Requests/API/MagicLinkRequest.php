<?php

namespace App\Http\Requests\API;

use App\Enums\OtpAuthAction;
use Illuminate\Foundation\Http\FormRequest;

class MagicLinkRequest extends FormRequest
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
        if ($this->type === OtpAuthAction::MAGIC_LINK_LOGIN) {
            return [
                'email' => 'required|email|exists:users,email'
            ];
        }

        if ($this->type === OtpAuthAction::MAGIC_LINK_REGISTRATION) {
            return [
                'email' => 'required|email|unique:users,email'
            ];
        }
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('auth.email_not_found'),
            'email.unique' => __('auth.email_already_exists'),
        ];
    }
}
