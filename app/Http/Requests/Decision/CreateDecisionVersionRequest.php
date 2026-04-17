<?php

namespace App\Http\Requests\Decision;

use App\Models\Decision;
use App\Enums\DecisionStatus;
use Illuminate\Foundation\Http\FormRequest;

class CreateDecisionVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $decision = Decision::findOrFail($this->route('decisionId'));
        $user = $this->user();

        // Seul le statut REVISION autorise de créer une nouvelle version
        if ($decision->status->value !== DecisionStatus::REVISION->value) {
            return false;
        }

        // Uniquement l'auteur original de la décision ou l'animateur
        $isAuthorOrAnimator = $decision->participants()->where('user_id', $user->id)
            ->whereIn('role', [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value])
            ->exists();

        return $isAuthorOrAnimator || $user->is_global_animator;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }
}
