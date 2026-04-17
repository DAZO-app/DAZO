<?php

namespace App\Http\Requests\Feedback;

use App\Enums\FeedbackStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeedbackStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Validation logic is inside the service to differentiate actor constraints
        return true; 
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(FeedbackStatus::class)],
        ];
    }
}
