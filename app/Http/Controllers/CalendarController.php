<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        $month = Carbon::create(
            $request->integer('year', $today->year),
            $request->integer('month', $today->month),
            1,
        )->startOfMonth();

        $gridStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $completions = HabitLog::query()
            ->where('completed', true)
            ->whereBetween('date', [$gridStart, $gridEnd])
            ->get()
            ->groupBy(fn (HabitLog $log) => $log->date->toDateString());

        $deadlines = Goal::query()
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [$gridStart, $gridEnd])
            ->get(['id', 'title', 'target_date'])
            ->groupBy(fn (Goal $goal) => $goal->target_date->toDateString());

        $habitCount = Habit::count();

        $days = [];
        $cursor = $gridStart->copy();

        while ($cursor->lessThanOrEqualTo($gridEnd)) {
            $key = $cursor->toDateString();

            $days[] = [
                'date' => $key,
                'day' => $cursor->day,
                'in_month' => $cursor->month === $month->month,
                'is_today' => $cursor->isSameDay($today),
                'completed_count' => isset($completions[$key]) ? $completions[$key]->count() : 0,
                'goals' => isset($deadlines[$key])
                    ? $deadlines[$key]->map(fn (Goal $g) => ['id' => $g->id, 'title' => $g->title])->values()
                    : [],
            ];

            $cursor->addDay();
        }

        return Inertia::render('Calendar/Index', [
            'days' => $days,
            'habitCount' => $habitCount,
            'label' => $month->copy()->locale('fr')->isoFormat('MMMM YYYY'),
            'prev' => ['year' => $month->copy()->subMonth()->year, 'month' => $month->copy()->subMonth()->month],
            'next' => ['year' => $month->copy()->addMonth()->year, 'month' => $month->copy()->addMonth()->month],
        ]);
    }
}
