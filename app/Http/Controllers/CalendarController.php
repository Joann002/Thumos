<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalTask;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\JournalEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();
        $userId = $request->user()->id;

        $month = Carbon::create(
            $request->integer('year', $today->year),
            $request->integer('month', $today->month),
            1,
        )->startOfMonth();

        $gridStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $completions = HabitLog::query()
            ->where('completed', true)
            ->whereRelation('habit', 'user_id', $userId)
            ->whereBetween('date', [$gridStart, $gridEnd])
            ->get()
            ->groupBy(fn (HabitLog $log) => $log->date->toDateString());

        $deadlines = Goal::query()
            ->where('user_id', $userId)
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [$gridStart, $gridEnd])
            ->get(['id', 'title', 'target_date'])
            ->groupBy(fn (Goal $goal) => $goal->target_date->toDateString());

        $habitCount = Habit::where('user_id', $userId)->count();

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

    public function show(Request $request, string $date): JsonResponse
    {
        $userId = $request->user()->id;
        $targetDate = Carbon::parse($date);

        // Récupérer les habitudes et leurs logs pour cette date
        $habits = Habit::where('user_id', $userId)
            ->with(['logs' => function ($query) use ($date) {
                $query->where('date', $date);
            }])
            ->get()
            ->map(function (Habit $habit) use ($targetDate) {
                $log = $habit->logs->first();
                return [
                    'id' => $habit->id,
                    'name' => $habit->name,
                    'color' => $habit->color,
                    'completed' => $log ? $log->completed : false,
                    'is_due' => $habit->isDueOn($targetDate),
                ];
            });

        // Récupérer les objectifs avec échéance ce jour-là
        $goals = Goal::where('user_id', $userId)
            ->where('target_date', $date)
            ->with(['tasks' => function ($query) {
                $query->orderBy('order');
            }])
            ->get()
            ->map(function (Goal $goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'description' => $goal->description,
                    'category' => $goal->category,
                    'status' => $goal->status,
                    'tasks' => $goal->tasks->map(fn (GoalTask $task) => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'done' => $task->done,
                    ]),
                ];
            });

        // Récupérer l'entrée de journal pour cette date
        $journal = JournalEntry::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        return response()->json([
            'date' => $targetDate->locale('fr')->isoFormat('dddd D MMMM YYYY'),
            'habits' => $habits,
            'goals' => $goals,
            'journal' => $journal ? [
                'id' => $journal->id,
                'content' => $journal->content,
                'mood' => $journal->mood,
            ] : null,
        ]);
    }
}
