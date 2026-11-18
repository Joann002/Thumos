<?php

namespace App\Http\Requests;

use App\Models\JournalEntry;

class UpdateJournalEntryRequest extends StoreJournalEntryRequest
{
    public function authorize(): bool
    {
        $model = $this->route('entry');

        return $this->user() !== null && $model instanceof JournalEntry
            && $model->user_id === $this->user()->id;
    }
}
