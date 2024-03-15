<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class FileUpdateRequest extends FormRequest
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
        $rules = [
            'orig_filename' => 'required|string|max:255',
        ];

        if (request('group_id')) {
            $rules['group_id'] = 'exists:groups,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'orig_filename' => __('messages.name-is-required')
        ];
    }
}
