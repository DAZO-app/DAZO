<?php

namespace App\Http\Requests\Invitation;

use App\Enums\InvitationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circle = \App\Models\Circle::findOrFail($this->input('circle_id'));
        return $this->user()->can('update', $circle);
    }

    public function rules(): array
    {
        return [
            'circle_id' => ['required', 'uuid', 'exists:circles,id'],
            'email' => ['required', 'email'],
            'role' => ['required', Rule::enum(InvitationRole::class)],
        ];
    }
}
