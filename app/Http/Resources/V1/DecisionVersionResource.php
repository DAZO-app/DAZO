<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DecisionVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'decision_id' => $this->decision_id,
            'author_id' => $this->author_id,
            'version_number' => $this->version_number,
            'is_current' => $this->is_current,
            'content' => $this->content,
            'change_reason' => $this->change_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'feedbacks' => FeedbackResource::collection($this->whenLoaded('feedbacks')),
            'author_role' => $this->when(isset($this->author_role), $this->author_role),
        ];
    }
}
