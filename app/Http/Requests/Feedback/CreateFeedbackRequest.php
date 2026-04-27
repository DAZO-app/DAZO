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

        // Phase de la décision vs Type requis
        $phase = $decision->status->value;
        $requestedType = $this->input('type');

        if ($phase === \App\Enums\DecisionStatus::CLARIFICATION->value && $requestedType !== \App\Enums\FeedbackType::CLARIFICATION->value) {
            return false;
        }

        if ($phase === \App\Enums\DecisionStatus::REACTION->value && $requestedType !== \App\Enums\FeedbackType::REACTION->value) {
            return false;
        }

        if ($phase === \App\Enums\DecisionStatus::OBJECTION->value && !in_array($requestedType, [\App\Enums\FeedbackType::OBJECTION->value, \App\Enums\FeedbackType::SUGGESTION->value])) {
            return false;
        }

        if (!in_array($phase, [\App\Enums\DecisionStatus::CLARIFICATION->value, \App\Enums\DecisionStatus::REACTION->value, \App\Enums\DecisionStatus::OBJECTION->value])) {
            return false;
        }

        // Bloquer la création de plusieurs retours du même type par un même utilsateur sur la même version
        $existingFeedback = \App\Models\Feedback::where('decision_version_id', $decision->currentVersion->id)
            ->where('author_id', $user->id)
            ->where('type', $requestedType)
            ->whereNotIn('status', [\App\Enums\FeedbackStatus::WITHDRAWN->value, \App\Enums\FeedbackStatus::REJECTED->value])
            ->exists();
            
        abort_if($existingFeedback, 422, "Vous avez déjà soumis un retour de ce type pour cette version.");

        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        $participant = $decision->participants()->where('user_id', $user->id)->first();
        $role = $participant?->role->value;

        // Le porteur ne peut jamais créer de feedback (il répond aux fils)
        // L'animateur ne crée jamais de feedback : il répond aux fils ouverts.
        // SAUF s'il agit au nom d'un autre participant
        if ($this->has('acting_as_user_id')) {
            $isSecretary = ($member && $member->role->value !== CircleMemberRole::OBSERVER->value) || $user->is_global_animator;

            if (!$isSecretary) {
                return false;
            }
            $targetUser = \App\Models\User::findOrFail($this->acting_as_user_id);
            // On vérifie que la cible est bien membre
            $targetMember = $decision->circle->members()->where('user_id', $targetUser->id)->first();
            if (!$targetMember || $targetMember->role->value === CircleMemberRole::OBSERVER->value) {
                return false;
            }
            return true;
        }

        if ($role === \App\Enums\DecisionParticipantRole::AUTHOR->value) {
            return false;
        }

        if ($role === \App\Enums\DecisionParticipantRole::ANIMATOR->value) {
            return false;
        }

        if (!$user->is_global_animator && (!$member || $member->role->value === CircleMemberRole::OBSERVER->value)) {
            return false; // Pas membre ou observer
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(FeedbackType::class)],
            'content' => ['required', 'string'],
            'acting_as_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
