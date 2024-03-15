<?php

namespace App\Http\Requests\API;

use App\Enums\PathBackgroundScope;
use App\Enums\PathBackgroundType;
use Illuminate\Foundation\Http\FormRequest;

class PathBackgroundRequest extends FormRequest
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
        $imageType = PathBackgroundType::IMAGE;

        if ($this->bg_type === $imageType) {
            return [
                'bg_value' => 'required|file|mimes:jpeg,jpg,png,gif|max:2048',
                'bg_type' => "nullable|string|in:$imageType",
            ];
        }

        return [
            'bg_value' => 'nullable|string'
        ];
    }
}
