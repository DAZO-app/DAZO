<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@dazo.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_global_animator' => true,
            ]
        );

        // Standard User
        User::updateOrCreate(
            ['email' => 'user@dazo.test'],
            [
                'name' => 'Standard User',
                'password' => Hash::make('password'),
                'role' => UserRole::USER,
                'is_global_animator' => false,
            ]
        );

        // Generate some random users
        User::factory(5)->create();
    }
}
