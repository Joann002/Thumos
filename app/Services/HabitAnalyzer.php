<?php

namespace App\Services;

use App\Models\Habit;
use Illuminate\Support\Collection;

class HabitAnalyzer
{
    public function __construct(private StreakCalculator $streakCalculator) {}

    /**
     * Get detailed analytics for a habit
     */
    public function getAnalytics(Habit $habit, int $days = 30): array
    {
        $summary = $habit->completionSummary($days);
        $expectedDays = $summary['expected'];
        $completedDays = $summary['completed'];

        return [
            'current_streak' => $this->streakCalculator->current($habit),
            'longest_streak' => $this->streakCalculator->longest($habit),
            'total_completions' => $this->streakCalculator->totalCompletions($habit),
            'completion_rate' => $summary['rate'],
            'expected_days' => $expectedDays,
            'completed_days' => $completedDays,
            'missed_days' => $expectedDays - $completedDays,
            'period_days' => $days,
        ];
    }

    /**
     * Get best performing habits
     */
    public function getBestPerformers(Collection $habits, int $limit = 5): Collection
    {
        return $habits->sortByDesc(function ($habit) {
            return $habit->getCompletionRate(30);
        })->take($limit);
    }

    /**
     * Get habits needing attention (low completion rate)
     */
    public function getNeedingAttention(Collection $habits, float $threshold = 50.0): Collection
    {
        return $habits->filter(function ($habit) use ($threshold) {
            return $habit->getCompletionRate(30) < $threshold;
        });
    }
}
