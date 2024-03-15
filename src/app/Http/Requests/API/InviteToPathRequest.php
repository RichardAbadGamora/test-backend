<?php

namespace App\Http\Requests\API;

use App\Models\Path;
use App\Rules\API\UserInPathRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteToPathRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->user_id) {
            $rules['user_id'] = [
                'required',
                new UserInPathRule($this->path_id),
            ];
        }

        if ($this->email) {
            $rules['email'] = [
                'required',
                'email',
                'unique:users,email'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'unique' => __('messages.user-already-part-of-the-system')
        ];
    }
}
