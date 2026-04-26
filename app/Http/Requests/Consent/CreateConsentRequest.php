<?php

namespace App\Http\Requests\Consent;

use App\Models\Decision;
use App\Models\DecisionVersion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $decision = Decision::findOrFail($this->route('id'));
        $version = DecisionVersion::findOrFail($this->route('versionId'));

        if ($version->decision_id !== $decision->id) return false;
        
        if (!in_array($decision->status->value, [\App\Enums\DecisionStatus::CLARIFICATION->value, \App\Enums\DecisionStatus::REACTION->value, \App\Enums\DecisionStatus::OBJECTION->value])) return false;

        $user = $this->user();

        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        if (!$member || $member->role->value === \App\Enums\CircleMemberRole::OBSERVER->value) {
            return false;
        }

        $participant = $decision->participants()->where('user_id', $user->id)->first();
        $role = $participant?->role->value;
        
        // Si l'utilisateur agit au nom de quelqu'un d'autre
        if ($this->has('acting_as_user_id')) {
            // Seul l'animateur (ou le porteur ?) peut agir au nom de quelqu'un
            if (!in_array($role, [\App\Enums\DecisionParticipantRole::ANIMATOR->value])) {
                return false;
            }
            return true;
        }
        
        if (in_array($role, [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value])) {
            return false;
        }
        
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['no_objection', 'abstention', 'no_questions', 'no_reaction'])],
            'acting_as_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
