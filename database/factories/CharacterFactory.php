<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
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
            'name' => fake()->firstName(),
            'race' => fake()->randomElement(['human', 'elf', 'dwarf', 'halfling', 'dragonborn']),
            'classes' => [
                ['class' => fake()->randomElement(['warrior', 'mage', 'rogue', 'cleric', 'ranger']), 'level' => fake()->numberBetween(1, 10)],
            ],
            'background' => fake()->randomElement(['Acolyte', 'Criminal', 'Folk Hero', 'Noble', 'Soldier']),
            'alignment' => fake()->randomElement(['Lawful Good', 'True Neutral', 'Chaotic Evil']),
            'level' => fake()->numberBetween(1, 10),
            'max_hp' => fake()->numberBetween(10, 100),
            'current_hp' => fake()->numberBetween(10, 100),
            'current_xp' => fake()->numberBetween(0, 10000),
            'strength' => fake()->numberBetween(8, 20),
            'dexterity' => fake()->numberBetween(8, 20),
            'constitution' => fake()->numberBetween(8, 20),
            'intelligence' => fake()->numberBetween(8, 20),
            'wisdom' => fake()->numberBetween(8, 20),
            'charisma' => fake()->numberBetween(8, 20),
            'inventory' => [
                ['name' => fake()->word(), 'quantity' => fake()->numberBetween(1, 5), 'weight' => fake()->randomFloat(1, 0, 10)],
            ],
            'backstory' => fake()->paragraphs(3, true),
            'appearance' => [
                'eyes' => fake()->safeColorName().' eyes',
                'skin' => fake()->safeColorName().' skin',
                'ears' => 'normal ears',
                'tail' => fake()->boolean() ? 'long tail' : 'no tail',
                'horns' => fake()->boolean() ? 'small horns' : 'no horns',
                'other' => fake()->sentence(),
            ],
        ];
    }
}
