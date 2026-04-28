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
        $alice   = User::where('email', 'admin@dazo.test')->first();
        $bob     = User::where('email', 'user@dazo.test')->first();
        $claire  = User::where('email', 'claire@dazo.test')->first();
        $david   = User::where('email', 'david@dazo.test')->first();
        $emma    = User::where('email', 'emma@dazo.test')->first();
        $franck  = User::where('email', 'franck@dazo.test')->first();
        $gaelle  = User::where('email', 'gaelle@dazo.test')->first();
        $hugo    = User::where('email', 'hugo@dazo.test')->first();

        // ── Cercle racine ─────────────────────────────────────────
        $coordination = Circle::updateOrCreate(
            ['name' => 'Coordination'],
            [
                'description' => 'Cercle de pilotage stratégique – définit la vision et arbitre les priorités transverses.',
                'type' => CircleType::CLOSED,
            ]
        );

        // ── Sous-cercles ──────────────────────────────────────────
        $tech = Circle::updateOrCreate(
            ['name' => 'Technique'],
            [
                'description' => 'Infrastructure, développement et architecture logicielle.',
                'type' => CircleType::OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        $rh = Circle::updateOrCreate(
            ['name' => 'RH & Culture'],
            [
                'description' => 'Bien-être, recrutement, onboarding et culture d\'entreprise.',
                'type' => CircleType::OBSERVER_OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        $finance = Circle::updateOrCreate(
            ['name' => 'Finance & Budget'],
            [
                'description' => 'Gestion budgétaire, achats et suivi de la trésorerie.',
                'type' => CircleType::CLOSED,
                'parent_id' => $coordination->id,
            ]
        );

        $produit = Circle::updateOrCreate(
            ['name' => 'Produit'],
            [
                'description' => 'Roadmap produit, UX/UI et priorisation des fonctionnalités.',
                'type' => CircleType::OPEN,
                'parent_id' => $coordination->id,
            ]
        );

        // ── Affectation des membres ───────────────────────────────

        $memberships = [
            // Coordination : Alice et Hugo animent, Claire membre
            [$coordination, $alice, CircleMemberRole::ANIMATOR],
            [$coordination, $hugo, CircleMemberRole::ANIMATOR],
            [$coordination, $claire, CircleMemberRole::MEMBER],

            // Tech : Claire anime, Bob/David/Franck/Emma membres
            [$tech, $claire, CircleMemberRole::ANIMATOR],
            [$tech, $bob, CircleMemberRole::MEMBER],
            [$tech, $david, CircleMemberRole::MEMBER],
            [$tech, $franck, CircleMemberRole::MEMBER],
            [$tech, $emma, CircleMemberRole::MEMBER],

            // RH : Alice anime, Emma/Gaëlle/Hugo membres
            [$rh, $alice, CircleMemberRole::ANIMATOR],
            [$rh, $emma, CircleMemberRole::MEMBER],
            [$rh, $gaelle, CircleMemberRole::MEMBER],
            [$rh, $hugo, CircleMemberRole::MEMBER],

            // Finance : Hugo anime, Alice/David membres
            [$finance, $hugo, CircleMemberRole::ANIMATOR],
            [$finance, $alice, CircleMemberRole::MEMBER],
            [$finance, $david, CircleMemberRole::MEMBER],

            // Produit : Bob anime, Alice/Claire/Emma/Franck membres
            [$produit, $bob, CircleMemberRole::ANIMATOR],
            [$produit, $alice, CircleMemberRole::MEMBER],
            [$produit, $claire, CircleMemberRole::MEMBER],
            [$produit, $emma, CircleMemberRole::MEMBER],
            [$produit, $franck, CircleMemberRole::MEMBER],
        ];

        foreach ($memberships as [$circle, $user, $role]) {
            if (!$user) continue;
            if (!$circle->members()->where('user_id', $user->id)->exists()) {
                $circle->members()->create([
                    'user_id' => $user->id,
                    'role' => $role,
                ]);
            }
        }
    }
}
