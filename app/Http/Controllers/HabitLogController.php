<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HabitLogController extends Controller
{
    /**
     * Toggle a habit's completion for a given day (defaults to today).
     */
    public function toggle(Request $request, Habit $habit): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::today();

        $existing = $habit->logs()->whereDate('date', $date)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $habit->logs()->create([
                'date' => $date->toDateString(),
                'completed' => true,
            ]);
        }

        return back();
    }
}
