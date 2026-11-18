<?php

namespace App\Http\Requests;

use App\Models\Goal;

class UpdateGoalRequest extends StoreGoalRequest
{
    public function authorize(): bool
    {
        $model = $this->route('goal');

        return $this->user() !== null && $model instanceof Goal
            && $model->user_id === $this->user()->id;
    }
}
