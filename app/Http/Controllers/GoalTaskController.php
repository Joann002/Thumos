<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GoalTaskController extends Controller
{
    public function store(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorizeGoal($goal);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($goal, $data) {
            $goal->newQuery()->whereKey($goal->id)->lockForUpdate()->firstOrFail();
            $goal->tasks()->create([
                'title' => $data['title'],
                'order' => (int) $goal->tasks()->max('order') + 1,
            ]);
        });

        return back();
    }

    public function update(Request $request, GoalTask $task): RedirectResponse
    {
        $this->authorizeGoal($task->goal);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'done' => ['sometimes', 'boolean'],
        ]);

        $task->update($data);

        return back();
    }

    public function destroy(GoalTask $task): RedirectResponse
    {
        $this->authorizeGoal($task->goal);

        $task->delete();

        return back();
    }

    public function reorder(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorizeGoal($goal);

        $data = $request->validate([
            'ids' => ['required', 'array', 'size:'.$goal->tasks()->count()],
            'ids.*' => ['required', 'integer', 'distinct', Rule::exists('goal_tasks', 'id')->where('goal_id', $goal->id)],
        ]);

        DB::transaction(function () use ($goal, $data) {
            $goal->newQuery()->whereKey($goal->id)->lockForUpdate()->firstOrFail();
            foreach ($data['ids'] as $position => $id) {
                $goal->tasks()->whereKey($id)->update(['order' => $position + 1]);
            }
        });

        return back();
    }

    private function authorizeGoal(Goal $goal): void
    {
        abort_if($goal->user_id !== Auth::id(), 403);
    }
}
