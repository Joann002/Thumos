<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Services\StreakCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HabitController extends Controller
{
    public function index(StreakCalculator $streaks): Response
    {
        $today = Carbon::today();

        $habits = Habit::query()
            ->with('logs')
            ->orderBy('name')
            ->get()
            ->map(function (Habit $habit) use ($today, $streaks) {
                $habit->setAttribute(
                    'done_today',
                    $habit->logs->contains(fn ($log) => $log->date->isSameDay($today)),
                );
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

    public function store(Request $request): RedirectResponse
    {
        Habit::create($this->validateHabit($request));

        return redirect()->route('habits.index')
            ->with('success', 'Habitude créée.');
    }

    public function edit(Habit $habit): Response
    {
        return Inertia::render('Habits/Edit', [
            'habit' => $habit,
        ]);
    }

    public function update(Request $request, Habit $habit): RedirectResponse
    {
        $habit->update($this->validateHabit($request));

        return redirect()->route('habits.index')
            ->with('success', 'Habitude mise à jour.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habitude supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateHabit(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'in:daily,weekly'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:1,7'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
