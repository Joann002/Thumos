<?php

namespace App\Http\Requests;

use App\Models\Habit;

class UpdateHabitRequest extends StoreHabitRequest
{
    public function authorize(): bool
    {
        $model = $this->route('habit');

        return $this->user() !== null && $model instanceof Habit
            && $model->user_id === $this->user()->id;
    }
}
