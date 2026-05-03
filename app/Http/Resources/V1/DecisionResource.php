<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DecisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'priority' => $this->priority,
            'emergency_mode' => $this->emergency_mode,
            'share_count' => $this->share_count,
            'circle_id' => $this->circle_id,
            'author_id' => $this->author_id,
            'animator_id' => $this->animator_id,
            'current_deadline' => $this->current_deadline,
            'objection_round_deadline' => $this->objection_round_deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations (optional loading)
            'circle' => new CircleResource($this->whenLoaded('circle')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'current_version' => new DecisionVersionResource($this->whenLoaded('currentVersion')),
            'author' => new UserResource($this->whenLoaded('author')),
        ];
    }
}
