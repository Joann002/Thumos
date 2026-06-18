<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HeatmapController extends Controller
{
    /**
     * Number of weeks shown in the heatmap (GitHub-style), ending this week.
     */
    private const WEEKS = 53;

    public function index(): Response
    {
        $today = Carbon::today();
        $end = $today->copy()->endOfWeek(Carbon::SUNDAY);
        $start = $end->copy()->subWeeks(self::WEEKS - 1)->startOfWeek(Carbon::MONDAY);

        $counts = HabitLog::query()
            ->where('completed', true)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy(fn (HabitLog $log) => $log->date->toDateString())
            ->map->count();

        $habitCount = Habit::count();

        $weeks = [];
        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $week = [];

            for ($d = 0; $d < 7; $d++) {
                $key = $cursor->toDateString();
                $count = (int) ($counts[$key] ?? 0);

                $week[] = [
                    'date' => $key,
                    'count' => $count,
                    'level' => $this->level($count, $habitCount),
                    'future' => $cursor->greaterThan($today),
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        $totalCompletions = $counts->sum();

        return Inertia::render('Heatmap/Index', [
            'weeks' => $weeks,
            'habitCount' => $habitCount,
            'totalCompletions' => $totalCompletions,
            'rangeLabel' => $start->copy()->locale('fr')->isoFormat('MMM YYYY')
                .' – '.$end->copy()->locale('fr')->isoFormat('MMM YYYY'),
        ]);
    }

    /**
     * Intensity level 0–4 based on share of habits completed that day.
     */
    private function level(int $count, int $habitCount): int
    {
        if ($count === 0) {
            return 0;
        }

        if ($habitCount <= 0) {
            return 4;
        }

        $ratio = $count / $habitCount;

        return match (true) {
            $ratio >= 1.0 => 4,
            $ratio >= 0.66 => 3,
            $ratio >= 0.33 => 2,
            default => 1,
        };
    }
}
