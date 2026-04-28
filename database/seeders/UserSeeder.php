<?php

namespace Database\Seeders;

use App\Enums\NotificationCategory;
use App\Enums\UserRole;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@dazo.test',
                'name' => 'Alice Durand',
                'role' => UserRole::SUPERADMIN,
                'is_global_animator' => true,
                'is_active' => true,
                'custom_views' => [
                    ['name' => 'Mes urgences', 'filters' => ['status' => ['objection', 'reaction'], 'priority' => 1]],
                    ['name' => 'Archivées', 'filters' => ['status' => ['adopted', 'abandoned']]],
                ],
            ],
            [
                'email' => 'user@dazo.test',
                'name' => 'Bob Martin',
                'role' => UserRole::USER,
                'is_global_animator' => false,
                'is_active' => true,
                'custom_views' => null,
            ],
            [
                'email' => 'claire@dazo.test',
                'name' => 'Claire Lefèvre',
                'role' => UserRole::USER,
                'is_global_animator' => true,
                'is_active' => true,
                'custom_views' => [
                    ['name' => 'En attente de moi', 'filters' => ['needs_my_action' => true]],
                ],
            ],
            [
                'email' => 'david@dazo.test',
                'name' => 'David Nguyen',
                'role' => UserRole::USER,
                'is_global_animator' => false,
                'is_active' => true,
                'custom_views' => null,
            ],
            [
                'email' => 'emma@dazo.test',
                'name' => 'Emma Petit',
                'role' => UserRole::USER,
                'is_global_animator' => false,
                'is_active' => true,
                'custom_views' => null,
            ],
            [
                'email' => 'franck@dazo.test',
                'name' => 'Franck Moreau',
                'role' => UserRole::USER,
                'is_global_animator' => false,
                'is_active' => true,
                'custom_views' => [
                    ['name' => 'Stratégie en cours', 'filters' => ['category' => 'Stratégie', 'status' => ['clarification', 'reaction', 'objection']]],
                ],
            ],
            [
                'email' => 'gaelle@dazo.test',
                'name' => 'Gaëlle Rousseau',
                'role' => UserRole::USER,
                'is_global_animator' => false,
                'is_active' => false, // Inactive user — for testing deactivated accounts
                'custom_views' => null,
            ],
            [
                'email' => 'hugo@dazo.test',
                'name' => 'Hugo Bernard',
                'role' => UserRole::ADMIN,
                'is_global_animator' => false,
                'is_active' => true,
                'custom_views' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }

        // ── Notification preferences variées ──────────────────────────
        $this->seedNotificationPreferences();
    }

    private function seedNotificationPreferences(): void
    {
        $prefsMap = [
            'claire@dazo.test' => [
                // Claire veut tout par email sauf les mentions
                NotificationCategory::NEW_DECISION->value => ['email' => true, 'web' => true],
                NotificationCategory::PHASE_CHANGE->value => ['email' => true, 'web' => true],
                NotificationCategory::FEEDBACK->value => ['email' => true, 'web' => true],
                NotificationCategory::MENTION->value => ['email' => false, 'web' => true],
                NotificationCategory::DEADLINE->value => ['email' => true, 'web' => true],
            ],
            'david@dazo.test' => [
                // David a désactivé presque tout
                NotificationCategory::NEW_DECISION->value => ['email' => false, 'web' => true],
                NotificationCategory::PHASE_CHANGE->value => ['email' => false, 'web' => true],
                NotificationCategory::FEEDBACK->value => ['email' => false, 'web' => false],
                NotificationCategory::MENTION->value => ['email' => false, 'web' => true],
                NotificationCategory::DEADLINE->value => ['email' => true, 'web' => true],
            ],
            'franck@dazo.test' => [
                // Franck ne veut que les deadlines et les changements de phase
                NotificationCategory::NEW_DECISION->value => ['email' => false, 'web' => false],
                NotificationCategory::PHASE_CHANGE->value => ['email' => true, 'web' => true],
                NotificationCategory::FEEDBACK->value => ['email' => false, 'web' => false],
                NotificationCategory::MENTION->value => ['email' => true, 'web' => true],
                NotificationCategory::DEADLINE->value => ['email' => true, 'web' => true],
            ],
        ];

        foreach ($prefsMap as $email => $categories) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;

            foreach ($categories as $category => $settings) {
                NotificationPreference::updateOrCreate(
                    ['user_id' => $user->id, 'category' => $category],
                    ['email_enabled' => $settings['email'], 'web_enabled' => $settings['web']]
                );
            }
        }
    }
}
