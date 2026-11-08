<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StreakCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatsApiController extends Controller
{
    public function __construct(private StreakCalculator $streakCalculator)
    {
    }

    /**
     * Get comprehensive statistics for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Goals statistics
        $totalGoals = $user->goals()->count();
        $activeGoals = $user->goals()->where('status', 'in_progress')->count();
        $completedGoals = $user->goals()->where('status', 'completed')->count();
        
        $goalsCompletedThisWeek = $user->goals()
            ->where('status', 'completed')
            ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Habits statistics
        $totalHabits = $user->habits()->count();
        $habitsCompletedToday = $user->habits()
            ->whereHas('logs', function ($query) {
                $query->whereDate('date', Carbon::today())
                    ->where('completed', true);
            })
            ->count();

        $totalStreaks = 0;
        $longestStreak = 0;
        
        foreach ($user->habits as $habit) {
            $currentStreak = $this->streakCalculator->current($habit);
            $totalStreaks += $currentStreak;
            $longestStreak = max($longestStreak, $this->streakCalculator->longest($habit));
        }

        // Journal statistics
        $totalJournalEntries = $user->journalEntries()->count();
        $entriesThisMonth = $user->journalEntries()
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        $lastEntryDate = $user->journalEntries()
            ->latest('date')
            ->value('date');

        return response()->json([
            'stats' => [
                'goals' => [
                    'total' => $totalGoals,
                    'active' => $activeGoals,
                    'completed' => $completedGoals,
                    'completed_this_week' => $goalsCompletedThisWeek,
                    'completion_rate' => $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100, 1) : 0,
                ],
                'habits' => [
                    'total' => $totalHabits,
                    'completed_today' => $habitsCompletedToday,
                    'today_completion_rate' => $totalHabits > 0 ? round(($habitsCompletedToday / $totalHabits) * 100, 1) : 0,
                    'total_streaks' => $totalStreaks,
                    'longest_streak' => $longestStreak,
                ],
                'journal' => [
                    'total_entries' => $totalJournalEntries,
                    'entries_this_month' => $entriesThisMonth,
                    'last_entry_date' => $lastEntryDate?->format('Y-m-d'),
                ],
            ],
        ]);
    }
}
