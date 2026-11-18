<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHabitRequest;
use App\Http\Requests\UpdateHabitRequest;
use App\Models\Habit;
use App\Services\StreakCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HabitController extends Controller
{
    public function index(Request $request, StreakCalculator $streaks): Response
    {
        $today = Carbon::today();

        $habits = $request->user()->habits()
            ->with('logs')
            ->orderBy('name')
            ->get()
            ->map(function (Habit $habit) use ($today, $streaks) {
                $habit->setAttribute(
                    'done_today',
                    $habit->logs->contains(fn ($log) => $log->completed && $log->date->isSameDay($today)),
                );
                $habit->setAttribute('is_due_today', $habit->isDueOn($today));
                $habit->setAttribute('current_streak', $streaks->current($habit));
                $habit->setAttribute('longest_streak', $streaks->longest($habit));
                $habit->unsetRelation('logs');

                return $habit;
            });

        return Inertia::render('Habits/Index', [
            'habits' => $habits,
            'today' => $today->toDateString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Habits/Create');
    }

    public function store(StoreHabitRequest $request): RedirectResponse
    {
        $request->user()->habits()->create($request->validated());

        return redirect()->route('habits.index')
            ->with('success', 'Habitude créée.');
    }

    public function edit(Habit $habit): Response
    {
        $this->authorizeOwner($habit);

        return Inertia::render('Habits/Edit', [
            'habit' => $habit,
        ]);
    }

    public function update(UpdateHabitRequest $request, Habit $habit): RedirectResponse
    {
        $this->authorizeOwner($habit);

        $habit->update($request->validated());

        return redirect()->route('habits.index')
            ->with('success', 'Habitude mise à jour.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $this->authorizeOwner($habit);

        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habitude supprimée.');
    }

    private function authorizeOwner(Habit $habit): void
    {
        abort_if($habit->user_id !== Auth::id(), 403);
    }
}
