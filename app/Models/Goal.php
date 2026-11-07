<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date',
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
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('done', true)->count();
        
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Check if the goal is overdue
     */
    public function isOverdue(): bool
    {
        return $this->target_date && 
               $this->target_date->isPast() && 
               $this->status !== 'completed';
    }
}
