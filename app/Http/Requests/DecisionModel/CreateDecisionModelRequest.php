<?php

namespace App\Http\Requests\DecisionModel;

use Illuminate\Foundation\Http\FormRequest;

class CreateDecisionModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\DecisionModel::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'template_content' => ['required', 'string'],
            'requires_distinct_animator' => ['boolean'],
            'default_objection_days' => ['integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }
}
