<?php

namespace Database\Seeders;

use App\Enums\CircleMemberRole;
use App\Enums\CircleType;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Database\Seeder;

class CircleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@dazo.test')->first();
        $standardUser = User::where('email', 'user@dazo.test')->first();
        $allUsers = User::all();

        $coordination = Circle::updateOrCreate(
            ['name' => 'Coordination'],
            [
                'description' => 'Cercle de pilotage global',
                'type' => CircleType::CLOSED,
            ]
        );

        $tech = Circle::updateOrCreate(
            ['name' => 'Technique'],
            [
                'description' => 'Cercle dédié à l\'infrastructure et au développement',
                'type' => CircleType::OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        $rh = Circle::updateOrCreate(
            ['name' => 'RH & Culture'],
            [
                'description' => 'Bien-être et recrutement',
                'type' => CircleType::OBSERVER_OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        // Admin = Animateur de tous les cercles
        foreach ([$coordination, $tech, $rh] as $circle) {
            if ($admin && !$circle->members()->where('user_id', $admin->id)->exists()) {
                $circle->members()->create([
                    'user_id' => $admin->id,
                    'role' => CircleMemberRole::ANIMATOR,
                ]);
            }
        }

        // Standard user = Membre de Tech et RH
        foreach ([$tech, $rh] as $circle) {
            if ($standardUser && !$circle->members()->where('user_id', $standardUser->id)->exists()) {
                $circle->members()->create([
                    'user_id' => $standardUser->id,
                    'role' => CircleMemberRole::MEMBER,
                ]);
            }
        }

        // Random users = Membres de Tech
        foreach ($allUsers->whereNotIn('email', ['admin@dazo.test', 'user@dazo.test']) as $user) {
            if (!$tech->members()->where('user_id', $user->id)->exists()) {
                $tech->members()->create([
                    'user_id' => $user->id,
                    'role' => CircleMemberRole::MEMBER,
                ]);
            }
        }
    }
}
