<?php

namespace App\Services;

use App\Models\Habit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class HabitAnalyzer
{
    public function __construct(private StreakCalculator $streakCalculator)
    {
    }

    /**
     * Get detailed analytics for a habit
     */
    public function getAnalytics(Habit $habit, int $days = 30): array
    {
        $logs = $habit->logs()
            ->where('date', '>=', Carbon::today()->subDays($days))
            ->where('completed', true)
            ->get();

        $expectedDays = $this->countExpectedDays($habit, $days);
        $completedDays = $logs->count();

        return [
            'current_streak' => $this->streakCalculator->current($habit),
            'longest_streak' => $this->streakCalculator->longest($habit),
            'total_completions' => $this->streakCalculator->totalCompletions($habit),
            'completion_rate' => $habit->getCompletionRate($days),
            'expected_days' => $expectedDays,
            'completed_days' => $completedDays,
            'missed_days' => $expectedDays - $completedDays,
            'period_days' => $days,
        ];
    }

    /**
     * Count expected days for a habit in a period
     */
    private function countExpectedDays(Habit $habit, int $days): int
    {
        $count = 0;
        $startDate = Carbon::today()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            if ($habit->isDueOn($date)) {
                $count++;
            }
        }

        return $count;
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
