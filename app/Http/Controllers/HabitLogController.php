<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HabitLogController extends Controller
{
    /**
     * Toggle a habit's completion for a given day (defaults to today).
     */
    public function toggle(Request $request, Habit $habit): RedirectResponse
    {
        abort_if($habit->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
        ]);

        $date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::today();

        validator(['date' => $date->toDateString()], [
            'date' => [fn ($attribute, $value, $fail) => $habit->isDueOn($date) ?: $fail('Cette habitude n’est pas prévue ce jour-là.')],
        ])->validate();

        DB::transaction(function () use ($habit, $date) {
            $habit->newQuery()->whereKey($habit->id)->lockForUpdate()->firstOrFail();
            $existing = $habit->logs()->whereDate('date', $date)->first();
            if ($existing && $existing->completed) {
                $existing->delete();
            } elseif ($existing) {
                $existing->update(['completed' => true]);
            } else {
                $habit->logs()->create(['date' => $date->toDateString(), 'completed' => true]);
            }
        });

        return back();
    }
}
