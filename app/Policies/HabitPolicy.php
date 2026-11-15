<?php

namespace App\Policies;

use App\Models\Habit;
use App\Models\User;

class HabitPolicy
{
    /**
     * Determine if the user can view the habit.
     */
    public function view(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }

    /**
     * Determine if the user can update the habit.
     */
    public function update(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the habit.
     */
    public function delete(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }
}
