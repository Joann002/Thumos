<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(GoalTask::class)->orderBy('order');
    }
}
