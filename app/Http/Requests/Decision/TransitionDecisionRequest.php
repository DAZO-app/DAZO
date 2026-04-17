<?php

namespace App\Http\Requests\Decision;

use App\Enums\DecisionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransitionDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorisation is checked inside the Service
    }

    public function rules(): array
    {
        return [
            'to' => ['required', Rule::enum(DecisionStatus::class)],
        ];
    }
}
