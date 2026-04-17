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
        
        // Phase Objection requise
        if ($decision->status->value !== \App\Enums\DecisionStatus::OBJECTION->value) return false;

        $user = $this->user();

        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        if (!$member || $member->role->value === \App\Enums\CircleMemberRole::OBSERVER->value) {
            return false;
        }

        $isAuthor = $decision->participants()->where('user_id', $user->id)
            ->where('role', \App\Enums\DecisionParticipantRole::AUTHOR->value)->exists();
        
        return !$isAuthor;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['no_objection', 'abstention'])],
        ];
    }
}
