<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'done',
        'order',
    ];

    protected $casts = [
        'done' => 'boolean',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
