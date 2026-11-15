<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(2),
            'frequency' => fake()->randomElement(['daily', 'weekly']),
            'days_of_week' => null,
            'color' => fake()->hexColor(),
        ];
    }

    /**
     * Indicate that the habit is daily.
     */
    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'daily',
            'days_of_week' => null,
        ]);
    }

    /**
     * Indicate that the habit is weekly.
     */
    public function weekly(array $days = [1, 3, 5]): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'weekly',
            'days_of_week' => $days,
        ]);
    }
}
