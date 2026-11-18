<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GoalController extends Controller
{
    public function index(Request $request): Response
    {
        $goals = $request->user()->goals()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($q) => $q->where('done', true),
            ])
            ->latest()
            ->get();

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Goals/Create');
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $goal = $request->user()->goals()->create($request->validated());

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif créé.');
    }

    public function show(Goal $goal): Response
    {
        $this->authorizeOwner($goal);

        $goal->load('tasks');

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
        ]);
    }

    public function edit(Goal $goal): Response
    {
        $this->authorizeOwner($goal);

        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
        ]);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorizeOwner($goal);

        $goal->update($request->validated());

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif mis à jour.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorizeOwner($goal);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé.');
    }

    private function authorizeOwner(Goal $goal): void
    {
        abort_if($goal->user_id !== Auth::id(), 403);
    }
}
