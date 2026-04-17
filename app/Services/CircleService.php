<?php

namespace App\Services;

use App\Enums\CircleMemberRole;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CircleService
{
    /**
     * Crée un cercle et assigne le créateur comme animateur.
     */
    public function createCircle(array $data, User $creator): Circle
    {
        return DB::transaction(function () use ($data, $creator) {
            $circle = Circle::create($data);

            $circle->members()->create([
                'user_id' => $creator->id,
                'role' => CircleMemberRole::ANIMATOR->value,
            ]);

            return $circle;
        });
    }

    /**
     * Exclut un membre s'il n'est pas l'auteur d'une décision en cours.
     */
    public function removeMember(Circle $circle, User $user): bool
    {
        // TODO: En Bloc 3, vérifier si le user est l'auteur d'une décision active dans ce cercle.
        return (bool) $circle->members()->where('user_id', $user->id)->delete();
    }
}
