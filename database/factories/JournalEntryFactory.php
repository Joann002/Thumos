<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-30 days', 'now'),
            'content' => fake()->paragraphs(3, true),
            'mood' => fake()->randomElement(['excellent', 'good', 'neutral', 'bad', 'terrible']),
        ];
    }

    /**
     * Indicate a positive mood entry.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'mood' => fake()->randomElement(['excellent', 'good']),
        ]);
    }

    /**
     * Indicate a negative mood entry.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'mood' => fake()->randomElement(['bad', 'terrible']),
        ]);
    }
}
