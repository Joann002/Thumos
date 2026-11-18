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
            'is_completed' => $goal->status === 'done',
            'is_overdue' => $goal->isOverdue(),
        ];
    }

    /**
     * Get goals grouped by status
     */
    public function groupByStatus(Collection $goals): array
    {
        return [
            'active' => $goals->where('status', 'active')->values(),
            'paused' => $goals->where('status', 'paused')->values(),
            'done' => $goals->where('status', 'done')->values(),
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
        $oneWeekFromNow = today()->addWeek();

        return $goals->filter(function ($goal) use ($oneWeekFromNow) {
            return $goal->target_date
                && $goal->target_date->gte(today())
                && $goal->target_date->lessThanOrEqualTo($oneWeekFromNow)
                && $goal->status === 'active';
        });
    }
}
