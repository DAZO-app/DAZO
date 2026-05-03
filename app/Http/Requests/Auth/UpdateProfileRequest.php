<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:2048'], // Max 2MB
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'custom_views' => ['sometimes', 'nullable', 'array'],
            'dashboard_widgets' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
