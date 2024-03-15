<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            'file' => 'required|file|max:25600',
            'page_hash' => 'required|string',
        ];

        if (request('group_id')) {
            $rules['group_id'] = 'exists:groups,id';
        }

        return $rules;
    }
}