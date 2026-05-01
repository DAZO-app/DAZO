<?php

namespace App\Policies;

use App\Enums\CircleMemberRole;
use App\Models\Circle;
use App\Models\User;

class CirclePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Circle $circle): bool
    {
        // Un cercle est visible par ses membres.
        // Si le cercle est 'open' ou 'observer_open', il peut éventuellement être visible publiquement.
        // Pour l'instant on autorise tout user connecté à voir ses infos basiques ou on restreint aux membres.
        // On va vérifier si le user est membre de ce cercle :
        if ($user->is_global_animator || in_array($user->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPERADMIN])) {
            return true;
        }

        return $circle->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Typiquement, config 'who_can_create_circles' => admin ou user.
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Circle $circle): bool
    {
        if ($user->is_global_animator || in_array($user->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPERADMIN])) {
            return true;
        }

        // L'utilisateur doit être un animateur de ce cercle.
        return $circle->members()->where('user_id', $user->id)
            ->where('role', CircleMemberRole::ANIMATOR->value)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Circle $circle): bool
    {
        return $this->update($user, $circle);
    }
}
