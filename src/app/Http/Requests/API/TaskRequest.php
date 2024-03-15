<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TaskRequest extends FormRequest
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
        if (is_null($this->segment(4))) {
            $rules = [
                'name' => 'required|string',
            ];

            if (request('task_id')) {
                $rules['task_id'] = 'exists:tasks,id';
            }
        }

        if ($this->segment(4) === 'status' && request('status')) {
            $rules['status'] = 'in:open,completed';
        }

        return $rules;
    }
}
