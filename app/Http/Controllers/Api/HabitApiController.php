<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Services\StreakCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HabitApiController extends Controller
{
    public function __construct(private StreakCalculator $streakCalculator)
    {
    }

    /**
     * Get all habits for the authenticated user with stats
     */
    public function index(Request $request): JsonResponse
    {
        $habits = $request->user()
            ->habits()
            ->with('logs')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($habit) {
                return [
                    'id' => $habit->id,
                    'name' => $habit->name,
                    'frequency' => $habit->frequency,
                    'days_of_week' => $habit->days_of_week,
                    'color' => $habit->color,
                    'current_streak' => $this->streakCalculator->current($habit),
                    'longest_streak' => $this->streakCalculator->longest($habit),
                    'total_completions' => $this->streakCalculator->totalCompletions($habit),
                    'completion_rate' => $habit->getCompletionRate(30),
                    'completed_today' => $habit->logs()
                        ->whereDate('date', Carbon::today())
                        ->where('completed', true)
                        ->exists(),
                ];
            });

        return response()->json(['habits' => $habits]);
    }

    /**
     * Get habit details with extended statistics
     */
    public function show(Request $request, Habit $habit): JsonResponse
    {
        if ($habit->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'habit' => [
                'id' => $habit->id,
                'name' => $habit->name,
                'frequency' => $habit->frequency,
                'days_of_week' => $habit->days_of_week,
                'color' => $habit->color,
                'current_streak' => $this->streakCalculator->current($habit),
                'longest_streak' => $this->streakCalculator->longest($habit),
                'total_completions' => $this->streakCalculator->totalCompletions($habit),
                'streak_breaks' => $this->streakCalculator->streakBreaks($habit),
                'completion_rate_7d' => $habit->getCompletionRate(7),
                'completion_rate_30d' => $habit->getCompletionRate(30),
                'completion_rate_90d' => $habit->getCompletionRate(90),
            ],
        ]);
    }
}
