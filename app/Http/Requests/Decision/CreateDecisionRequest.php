<?php

namespace App\Http\Requests\Decision;

use App\Enums\DecisionVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $circle = \App\Models\Circle::findOrFail($this->route('circle'));
        return $this->user()->can('create', [\App\Models\Decision::class, $circle]);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'model_id' => ['nullable', 'uuid', 'exists:decision_models,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'visibility' => ['sometimes', Rule::enum(DecisionVisibility::class)],
            'priority' => ['sometimes', 'integer'],
            'emergency_mode' => ['sometimes', 'boolean'],
            'animator_id' => ['nullable', 'uuid', 'exists:users,id'],
        ];
    }
}
