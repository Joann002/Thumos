<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get the user's initials for avatar display
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get total active goals count
     */
    public function getActiveGoalsCountAttribute(): int
    {
        return $this->goals()->where('status', 'active')->count();
    }

    /**
     * Get total active habits count
     */
    public function getActiveHabitsCountAttribute(): int
    {
        return $this->habits()->count();
    }
}
