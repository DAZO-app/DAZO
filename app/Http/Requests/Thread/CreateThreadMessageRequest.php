<?php

namespace App\Http\Requests\Thread;

use App\Models\Decision;
use App\Enums\CircleMemberRole;
use Illuminate\Foundation\Http\FormRequest;

class CreateThreadMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $decision = Decision::findOrFail($this->route('id'));
        $user = $this->user();

        // Must be in clarification or reaction phase (or other allowed)
        if (!in_array($decision->status->value, [\App\Enums\DecisionStatus::CLARIFICATION->value, \App\Enums\DecisionStatus::REACTION->value])) {
            return false;
        }

        // Must be a member of the circle, but NOT an observer
        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        if (!$member || $member->role->value === CircleMemberRole::OBSERVER->value) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'is_moderator_note' => ['sometimes', 'boolean'],
        ];
    }
}
