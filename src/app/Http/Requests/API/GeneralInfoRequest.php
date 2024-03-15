<?php

namespace App\Http\Requests\API;

use App\Enums\PathBackgroundScope;
use App\Enums\UserSettingTypes;
use Illuminate\Foundation\Http\FormRequest;

class GeneralInfoRequest extends FormRequest
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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
        ];
    }
}
