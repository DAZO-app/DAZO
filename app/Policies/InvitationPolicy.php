<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    /**
     * Pour inviter quelqu'un, il faut pouvoir modifier le cercle (animateur).
     */
    public function create(User $user, \App\Models\Circle $circle): bool
    {
        return $user->can('update', $circle);
    }
}
