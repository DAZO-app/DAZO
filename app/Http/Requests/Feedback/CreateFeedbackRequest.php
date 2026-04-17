<?php

namespace App\Http\Requests\Feedback;

use App\Models\Decision;
use App\Enums\CircleMemberRole;
use App\Enums\FeedbackType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $decision = Decision::findOrFail($this->route('id'));
        $user = $this->user();

        // Phase Objection requise
        if ($decision->status->value !== \App\Enums\DecisionStatus::OBJECTION->value) {
            return false;
        }

        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        if (!$member || $member->role->value === CircleMemberRole::OBSERVER->value) {
            return false; // Pas membre ou observer
        }

        // L'auteur ne peut pas s'auto-objecter
        $isAuthor = $decision->participants()->where('user_id', $user->id)
            ->where('role', \App\Enums\DecisionParticipantRole::AUTHOR->value)->exists();
        if ($isAuthor) return false;

        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(FeedbackType::class)],
            'content' => ['required', 'string'],
        ];
    }
}
