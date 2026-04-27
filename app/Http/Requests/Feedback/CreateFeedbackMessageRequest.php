<?php

namespace App\Http\Requests\Feedback;

use App\Models\Feedback;
use Illuminate\Foundation\Http\FormRequest;

class CreateFeedbackMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $feedback = Feedback::findOrFail($this->route('feedbackId'));
        $user = $this->user();
        $decision = $feedback->decisionVersion->decision;

        // Seul l'auteur du feedback, et l'auteur/animateur de la décision peuvent parler ici.
        $isFeedbackAuthor = $feedback->author_id === $user->id;
        
        $isDecisionAuthorOrAnimator = $decision->participants()
            ->where('user_id', $user->id)
            ->whereIn('role', [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value])
            ->exists();

        $isAdmin = in_array(optional($user->role)->value, ['superadmin', 'admin']);
        
        $isCircleMember = $decision->circle->members()
            ->where('user_id', $user->id)
            ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value)
            ->exists();

        return $isFeedbackAuthor || $isDecisionAuthorOrAnimator || $user->is_global_animator || $isAdmin || $isCircleMember;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'acting_as_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
