<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'decision_version_id' => $this->decision_version_id,
            'author_id' => $this->author_id,
            'type' => $this->type,
            'status' => $this->status,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'author' => new UserResource($this->whenLoaded('author')),
            'author_role' => $this->when(isset($this->author_role), $this->author_role),
            'joins_count' => $this->whenCounted('joins'),
            'messages_count' => $this->whenCounted('messages'),
        ];
    }
}
