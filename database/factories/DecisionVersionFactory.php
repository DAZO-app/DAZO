<?php

namespace Database\Factories;

use App\Models\Decision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DecisionVersion>
 */
class DecisionVersionFactory extends Factory
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
            'author_id' => User::factory(),
            'previous_version_id' => null,
            'version_number' => 1,
            'is_current' => true,
            'content' => $this->faker->paragraphs(3, true),
            'change_reason' => null,
        ];
    }
}
