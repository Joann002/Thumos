<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GoalController extends Controller
{
    public function index(): Response
    {
        $goals = Goal::query()
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateGoal($request);

        $goal = Goal::create($data);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif créé.');
    }

    public function show(Goal $goal): Response
    {
        $goal->load('tasks');

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
        ]);
    }

    public function edit(Goal $goal): Response
    {
        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
        ]);
    }

    public function update(Request $request, Goal $goal): RedirectResponse
    {
        $data = $this->validateGoal($request);

        $goal->update($data);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif mis à jour.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateGoal(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,done,paused'],
        ]);
    }
}
