<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
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
            return in_array($date->dayOfWeekIso, $this->days_of_week, true);
        }

        return true;
    }
}
