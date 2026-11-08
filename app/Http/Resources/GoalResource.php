<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'target_date' => $this->target_date?->format('Y-m-d'),
            'status' => $this->status,
            'progress' => $this->getProgressPercentage(),
            'is_overdue' => $this->isOverdue(),
            'tasks' => GoalTaskResource::collection($this->whenLoaded('tasks')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
