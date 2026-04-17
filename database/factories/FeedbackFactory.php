<?php

namespace Database\Factories;

use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\DecisionVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'decision_version_id' => DecisionVersion::factory(),
            'author_id' => User::factory(),
            'type' => $this->faker->randomElement(FeedbackType::cases()),
            'status' => FeedbackStatus::SUBMITTED,
            'content' => $this->faker->sentence(),
        ];
    }
}
