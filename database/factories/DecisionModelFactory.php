<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DecisionModel>
 */
class DecisionModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'template_content' => "# Template\n\n- Point 1\n- Point 2",
            'requires_distinct_animator' => false,
            'default_objection_days' => 7,
            'is_active' => true,
        ];
    }
}
