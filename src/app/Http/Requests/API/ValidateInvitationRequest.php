<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ValidateInvitationRequest extends FormRequest
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
            'token' => 'required|string|exists:invitations,token',
            'email' => 'required|string|exists:invitations,invitee_email',
        ];
    }
}
