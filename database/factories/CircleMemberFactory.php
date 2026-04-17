<?php

namespace Database\Factories;

use App\Enums\CircleMemberRole;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CircleMember>
 */
class CircleMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'circle_id' => Circle::factory(),
            'user_id' => User::factory(),
            'role' => CircleMemberRole::MEMBER,
        ];
    }
}
