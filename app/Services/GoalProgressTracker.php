<?php

namespace App\Services;

use App\Models\Goal;
use Illuminate\Support\Collection;

class GoalProgressTracker
{
    /**
     * Get progress summary for a goal
     */
    public function getSummary(Goal $goal): array
    {
        $tasks = $goal->tasks;
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('done', true)->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'remaining_tasks' => $totalTasks - $completedTasks,
            'percentage' => $goal->getProgressPercentage(),
            'is_completed' => $goal->status === 'completed',
            'is_overdue' => $goal->isOverdue(),
        ];
    }

    /**
     * Get goals grouped by status
     */
    public function groupByStatus(Collection $goals): array
    {
        return [
            'planned' => $goals->where('status', 'planned')->values(),
            'in_progress' => $goals->where('status', 'in_progress')->values(),
            'completed' => $goals->where('status', 'completed')->values(),
            'archived' => $goals->where('status', 'archived')->values(),
        ];
    }

    /**
     * Get overdue goals
     */
    public function getOverdue(Collection $goals): Collection
    {
        return $goals->filter(function ($goal) {
            return $goal->isOverdue();
        });
    }

    /**
     * Get goals due soon (within next 7 days)
     */
    public function getDueSoon(Collection $goals): Collection
    {
        $oneWeekFromNow = now()->addWeek();

        return $goals->filter(function ($goal) use ($oneWeekFromNow) {
            return $goal->target_date 
                && $goal->target_date->isFuture() 
                && $goal->target_date->lessThanOrEqualTo($oneWeekFromNow)
                && $goal->status !== 'completed';
        });
    }
}
