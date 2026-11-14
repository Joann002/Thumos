<?php

namespace App\Listeners;

use App\Events\GoalCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendGoalCompletionNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(GoalCompleted $event): void
    {
        Log::info('Goal completed', [
            'goal_id' => $event->goal->id,
            'user_id' => $event->goal->user_id,
            'title' => $event->goal->title,
        ]);

        // TODO: Send email notification
        // TODO: Create in-app notification
    }
}
