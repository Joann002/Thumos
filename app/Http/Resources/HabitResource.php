<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'frequency' => $this->frequency,
            'days_of_week' => $this->days_of_week,
            'color' => $this->color,
            'completion_rate' => $this->getCompletionRate(30),
            'logs' => HabitLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
