<?php

namespace App\Http\Requests\API;

use App\Enums\PathBackgroundScope;
use App\Enums\UserSettingTypes;
use Illuminate\Foundation\Http\FormRequest;

class ChangeEmailRequest extends FormRequest
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
        return [
            'email' => 'required|email',
            'state_id' => 'required|string',
            'otp' => 'required|numeric'
        ];
    }
}
