<?php

namespace App\Policies;

use App\Models\Decision;
use App\Models\User;

class DecisionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Decision $decision): bool
    {
        return $user->can('view', $decision->circle);
    }

    /**
     * Determine whether the user can create models in this circle.
     */
    public function create(User $user, \App\Models\Circle $circle): bool
    {
        return $user->can('view', $circle); // Any member of a circle can propose a decision.
    }

    /**
     * Determine whether the user can update the model (e.g., submit a new version).
     */
    public function update(User $user, Decision $decision): bool
    {
        // Seulement l'auteur ou l'animateur (en révision).
        $isAuthor = $decision->participants()->where('user_id', $user->id)
            ->where('role', \App\Enums\DecisionParticipantRole::AUTHOR->value)->exists();
        
        $isAnimator = $decision->participants()->where('user_id', $user->id)
            ->where('role', \App\Enums\DecisionParticipantRole::ANIMATOR->value)->exists();
        
        return $isAuthor || $isAnimator || $user->is_global_animator;
    }

    public function delete(User $user, Decision $decision): bool
    {
        return $decision->status->value === \App\Enums\DecisionStatus::DRAFT->value
            && $this->update($user, $decision);
    }
}
