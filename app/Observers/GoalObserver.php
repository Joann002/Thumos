<?php

namespace App\Observers;

use App\Events\GoalCompleted;
use App\Models\Goal;

class GoalObserver
{
    /**
     * Handle the Goal "updating" event.
     */
    public function updating(Goal $goal): void
    {
        // Check if status changed to completed
        if ($goal->isDirty('status') && $goal->status === 'completed') {
            event(new GoalCompleted($goal));
        }
    }
}
