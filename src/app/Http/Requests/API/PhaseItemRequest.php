<?php

namespace App\Http\Requests\API;

use App\Enums\FileAction;
use App\Enums\PhaseItemContentType;
use Illuminate\Foundation\Http\FormRequest;

class PhaseItemRequest extends FormRequest
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
        $linkType = PhaseItemContentType::LINK;
        $attachmentType = PhaseItemContentType::ATTACHMENT;

        $phaseItem = request('phaseItem');
        $fileAction = request('file_action');

        $rules = [
            'name' => 'required',
            'phase_id' => 'required|exists:phases,id',
        ];

        $contentType = request('content_type');

        $allowedTypes = flatten_enum(PhaseItemContentType::class);

        if ($contentType) {
            $rules['content_type'] = 'in:' . implode(',', $allowedTypes);
        }

        if ($contentType == $linkType) {
            $rules['content_value'] = 'required|url';
        }

        if ($contentType == $attachmentType) {
            if (!$phaseItem) {
                // if creating
                $rules['content_value'] = 'required|file|max:25600';
            } elseif ($fileAction === FileAction::CHANGE) {
                // if updating
                $rules['content_value'] = 'required|file|max:25600';
            }
        }

        return $rules;
    }
}
