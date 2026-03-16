<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'narration_detail_level' => fake()->randomElement(['succinct', 'normal', 'detailed']),
            'difficulty' => fake()->randomElement(['easy', 'normal', 'hard']),
            'starting_level' => fake()->numberBetween(1, 10),
            'play_style' => fake()->randomElement(['combat_focused', 'roleplay', 'balanced']),
            'progression_type' => fake()->randomElement(['xp', 'milestone']),
        ];
    }
}
