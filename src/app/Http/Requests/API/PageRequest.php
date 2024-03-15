<?php

namespace App\Http\Requests\API;

use App\Enums\PageType;
use App\Enums\Access;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
        $allowedTypes = flatten_enum(PageType::class);
        $allowedAccess = flatten_enum(Access::class);

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', $allowedTypes),
            'access' => 'required|string|in:' . implode(',', $allowedAccess)
        ];

        $rules = $this->additionalRules($rules);

        return $rules;
    }

    public function additionalRules($rules): array
    {
        if (in_array($this->type, [PageType::FIGMA_DESIGN_EMBED, PageType::FIGMA_PROTOTYPE_EMBED])) {
            $rules['embed_code'] = 'required|string';
        }

        return $rules;
    }
}
