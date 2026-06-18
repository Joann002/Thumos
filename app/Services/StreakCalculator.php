<?php

namespace App\Services;

use App\Models\Habit;
use Illuminate\Support\Carbon;

/**
 * Computes habit streaks from completion logs.
 *
 * A streak is a run of consecutive *scheduled* days that were completed:
 *  - daily habits: every calendar day is scheduled;
 *  - weekly habits: only the days listed in `days_of_week` (ISO 1=Mon..7=Sun).
 *
 * The current streak grants a grace period for today: if today is scheduled
 * but not yet completed, the streak is not considered broken.
 */
class StreakCalculator
{
    /**
     * Current ongoing streak, counting back from today.
     */
    public function current(Habit $habit): int
    {
        $completed = $this->completedDates($habit);

        if ($completed === []) {
            return 0;
        }

        $today = Carbon::today();
        $lower = Carbon::parse(array_key_first($completed));

        $streak = 0;
        $index = 0;
        $cursor = $today->copy();

        while ($cursor->greaterThanOrEqualTo($lower)) {
            if ($this->isScheduled($habit, $cursor)) {
                if (isset($completed[$cursor->toDateString()])) {
                    $streak++;
                } elseif ($index === 0 && $cursor->isSameDay($today)) {
                    // Today is scheduled but not yet checked: don't break the streak.
                    $index++;
                    $cursor->subDay();
                    continue;
                } else {
                    break;
                }

                $index++;
            }

            $cursor->subDay();
        }

        return $streak;
    }

    /**
     * Longest streak ever achieved.
     */
    public function longest(Habit $habit): int
    {
        $completed = $this->completedDates($habit);

        if ($completed === []) {
            return 0;
        }

        $cursor = Carbon::parse(array_key_first($completed));
        $today = Carbon::today();

        $best = 0;
        $run = 0;

        while ($cursor->lessThanOrEqualTo($today)) {
            if ($this->isScheduled($habit, $cursor)) {
                if (isset($completed[$cursor->toDateString()])) {
                    $run++;
                    $best = max($best, $run);
                } else {
                    $run = 0;
                }
            }

            $cursor->addDay();
        }

        return $best;
    }

    /**
     * Completed dates as an ordered ['Y-m-d' => true] map (ascending).
     *
     * @return array<string, true>
     */
    private function completedDates(Habit $habit): array
    {
        $dates = $habit->logs()
            ->where('completed', true)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        return array_fill_keys($dates, true);
    }

    private function isScheduled(Habit $habit, Carbon $date): bool
    {
        return $habit->isDueOn($date);
    }
}
