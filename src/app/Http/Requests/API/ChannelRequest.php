<?php

namespace App\Http\Requests\API;

use App\Enums\PathBackgroundType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChannelRequest extends FormRequest
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
            'name' => 'required|max:255',
            'channel_id' => [
                'exists:channels,id',
                Rule::exists('channels', 'id')->where(function ($query) {
                    $query->whereNull('parent_id');
                })
            ],
            'path_id' => 'required|exists:paths,id',
        ];
    }
}
