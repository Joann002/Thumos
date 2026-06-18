<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Services\StreakCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, StreakCalculator $streaks): Response
    {
        $user = $request->user();
        $today = Carbon::today();

        $habits = $user->habits()->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();

        $dueToday = $habits->filter(fn (Habit $h) => $h->isDueOn($today));
        $doneToday = $dueToday->filter(fn (Habit $h) => $h->logs->isNotEmpty());

        $todayHabits = $dueToday->map(fn (Habit $h) => [
            'id' => $h->id,
            'name' => $h->name,
            'color' => $h->color,
            'done_today' => $h->logs->isNotEmpty(),
        ])->values();

        $bestStreak = $habits->isEmpty()
            ? 0
            : $habits->max(fn (Habit $h) => $streaks->current($h));

        $upcomingGoals = $user->goals()
            ->whereNotNull('target_date')
            ->where('status', '!=', 'done')
            ->whereDate('target_date', '>=', $today)
            ->orderBy('target_date')
            ->limit(5)
            ->get(['id', 'title', 'target_date', 'category']);

        return Inertia::render('Dashboard', [
            'today' => $today->toDateString(),
            'stats' => [
                'completion' => $dueToday->count() > 0
                    ? (int) round($doneToday->count() / $dueToday->count() * 100)
                    : 0,
                'done_today' => $doneToday->count(),
                'due_today' => $dueToday->count(),
                'best_streak' => $bestStreak,
                'active_goals' => $user->goals()->where('status', 'active')->count(),
                'journal_entries' => $user->journalEntries()->count(),
            ],
            'todayHabits' => $todayHabits,
            'upcomingGoals' => $upcomingGoals,
        ]);
    }
}
