<?php

namespace App\Policies;

use App\Models\DecisionModel;
use App\Models\User;

class DecisionModelPolicy
{
    /**
     * Tous les utilisateurs peuvent voir la liste des modèles actifs.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DecisionModel $decisionModel): bool
    {
        return $decisionModel->is_active || $user->is_global_animator;
    }

    /**
     * Uniquement les admins globaux peuvent créer des modèles.
     */
    public function create(User $user): bool
    {
        return $user->is_global_animator;
    }

    public function update(User $user, DecisionModel $decisionModel): bool
    {
        return $user->is_global_animator;
    }

    public function delete(User $user, DecisionModel $decisionModel): bool
    {
        return $user->is_global_animator;
    }
}
