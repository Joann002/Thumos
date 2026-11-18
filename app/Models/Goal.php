<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    public const STATUSES = ['active', 'paused', 'done'];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(GoalTask::class)->orderBy('order');
    }

    /**
     * Get the progress percentage of completed tasks
     */
    public function getProgressPercentage(): int
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get(['done']);
        $totalTasks = $tasks->count();

        return $totalTasks > 0 ? (int) round($tasks->where('done', true)->count() / $totalTasks * 100) : 0;
    }

    /**
     * Check if the goal is overdue
     */
    public function isOverdue(): bool
    {
        return $this->target_date !== null
            && $this->target_date->lt(today())
            && $this->status === 'active';
    }
}
