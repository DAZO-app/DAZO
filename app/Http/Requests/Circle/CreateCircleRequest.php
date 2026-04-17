<?php

namespace App\Http\Requests\Circle;

use App\Enums\CircleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCircleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Circle::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(CircleType::class)],
            'parent_id' => ['nullable', 'uuid', 'exists:circles,id'],
        ];
    }
}
