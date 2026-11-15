<?php

namespace App\Policies;

use App\Models\JournalEntry;
use App\Models\User;

class JournalEntryPolicy
{
    /**
     * Determine if the user can view the journal entry.
     */
    public function view(User $user, JournalEntry $entry): bool
    {
        return $entry->user_id === $user->id;
    }

    /**
     * Determine if the user can update the journal entry.
     */
    public function update(User $user, JournalEntry $entry): bool
    {
        return $entry->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the journal entry.
     */
    public function delete(User $user, JournalEntry $entry): bool
    {
        return $entry->user_id === $user->id;
    }
}
