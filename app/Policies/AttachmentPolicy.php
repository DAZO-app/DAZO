<?php

namespace App\Policies;

use App\Enums\DecisionParticipantRole;
use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    public function delete(User $user, Attachment $attachment): bool
    {
        $version = $attachment->version;
        $decision = $version?->decision;

        if (! $decision) {
            return $attachment->uploader_id === $user->id || $user->is_global_animator;
        }

        $isDecisionManager = $decision->participants()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->exists();

        return $attachment->uploader_id === $user->id || $isDecisionManager || $user->is_global_animator;
    }

    public function update(User $user, Attachment $attachment): bool
    {
        return $this->delete($user, $attachment);
    }
}
