<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'frequency',
        'days_of_week',
        'color',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Whether the habit is scheduled on the given date.
     * Daily habits are always due; weekly habits only on their configured days.
     */
    public function isDueOn(Carbon $date): bool
    {
        if ($this->frequency === 'weekly' && ! empty($this->days_of_week)) {
            return in_array($date->dayOfWeekIso, array_map('intval', $this->days_of_week), true);
        }

        return true;
    }

    /**
     * Get the completion rate for the habit as a percentage
     */
    public function getCompletionRate(int $days = 30): float
    {
        return $this->completionSummary($days)['rate'];
    }

    /** Exactly $days calendar dates, including today, with one log query. */
    public function completionSummary(int $days = 30): array
    {
        if ($days < 1 || $days > 3660) {
            throw new \InvalidArgumentException('The period must be between 1 and 3660 days.');
        }

        $today = Carbon::today();
        $start = $today->copy()->subDays($days - 1);
        $logs = $this->relationLoaded('logs') ? $this->logs : $this->logs()
            ->where('completed', true)->whereBetween('date', [$start, $today->copy()->endOfDay()])->get();
        $completedDates = $logs->filter(fn ($log) => $log->completed
            && $log->date->betweenIncluded($start, $today))
            ->keyBy(fn ($log) => $log->date->toDateString());
        $expected = $completed = 0;

        for ($date = $start->copy(); $date->lte($today); $date->addDay()) {
            if ($this->isDueOn($date)) {
                $expected++;
                $completed += $completedDates->has($date->toDateString()) ? 1 : 0;
            }
        }

        return ['expected' => $expected, 'completed' => $completed,
            'rate' => $expected > 0 ? round($completed / $expected * 100, 2) : 0.0];
    }
}
