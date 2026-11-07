<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalApiController extends Controller
{
    /**
     * Get all goals for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $goals = $request->user()
            ->goals()
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'description' => $goal->description,
                    'category' => $goal->category,
                    'target_date' => $goal->target_date?->format('Y-m-d'),
                    'status' => $goal->status,
                    'progress' => $goal->getProgressPercentage(),
                    'is_overdue' => $goal->isOverdue(),
                    'tasks_count' => $goal->tasks->count(),
                    'completed_tasks_count' => $goal->tasks->where('done', true)->count(),
                ];
            });

        return response()->json(['goals' => $goals]);
    }

    /**
     * Get a single goal with its tasks
     */
    public function show(Request $request, Goal $goal): JsonResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            abort(403);
        }

        $goal->load('tasks');

        return response()->json([
            'goal' => [
                'id' => $goal->id,
                'title' => $goal->title,
                'description' => $goal->description,
                'category' => $goal->category,
                'target_date' => $goal->target_date?->format('Y-m-d'),
                'status' => $goal->status,
                'progress' => $goal->getProgressPercentage(),
                'is_overdue' => $goal->isOverdue(),
                'tasks' => $goal->tasks,
            ],
        ]);
    }
}
