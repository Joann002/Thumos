<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'name',
        'frequency',
        'days_of_week',
        'color',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }
}
