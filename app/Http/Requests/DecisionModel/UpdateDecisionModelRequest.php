<?php

namespace App\Http\Requests\DecisionModel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDecisionModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('model'));
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'template_content' => ['sometimes', 'string'],
            'requires_distinct_animator' => ['sometimes', 'boolean'],
            'default_objection_days' => ['sometimes', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
