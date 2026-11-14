<?php

namespace App\Listeners;

use App\Events\HabitStreakMilestone;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CelebrateHabitStreak implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(HabitStreakMilestone $event): void
    {
        $milestones = [7, 14, 30, 60, 90, 180, 365];

        if (in_array($event->streak, $milestones)) {
            Log::info('Habit streak milestone reached', [
                'habit_id' => $event->habit->id,
                'user_id' => $event->habit->user_id,
                'habit_name' => $event->habit->name,
                'streak' => $event->streak,
            ]);

            // TODO: Send celebration notification
            // TODO: Award badge
        }
    }
}
