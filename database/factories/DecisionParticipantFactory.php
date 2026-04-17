<?php

namespace Database\Factories;

use App\Enums\DecisionParticipantRole;
use App\Models\Decision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DecisionParticipant>
 */
class DecisionParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'decision_id' => Decision::factory(),
            'user_id' => User::factory(),
            'role' => DecisionParticipantRole::PARTICIPANT,
        ];
    }
}
