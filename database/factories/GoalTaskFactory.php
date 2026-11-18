<?php

namespace Database\Factories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalTaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'goal_id' => Goal::factory(),
            'title' => fake()->sentence(3),
            'done' => false,
            'order' => 1,
        ];
    }
}
