<?php

namespace Database\Factories;

use App\Enums\DecisionStatus;
use App\Enums\DecisionVisibility;
use App\Models\Category;
use App\Models\Circle;
use App\Models\DecisionModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Decision>
 */
class DecisionFactory extends Factory
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
            'model_id' => DecisionModel::factory(),
            'status' => $this->faker->randomElement(DecisionStatus::cases()),
            'title' => $this->faker->sentence(),
            'visibility' => DecisionVisibility::PUBLIC,
            'priority' => 0,
            'emergency_mode' => false,
            'objection_round_deadline' => null,
        ];
    }
}
