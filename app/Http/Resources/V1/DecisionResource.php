<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\AttachmentResource;

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
            'versions' => DecisionVersionResource::collection($this->whenLoaded('versions')),
            'author' => $this->whenLoaded('author', function() {
                if (!$this->author || !$this->author->user) return null;
                return [
                    'user' => [
                        'id' => $this->author->user->id,
                        'name' => $this->author->user->name,
                        'avatar_url' => $this->author->user->avatar_url,
                    ],
                    'role' => $this->author->role,
                ];
            }),
            'current_animator' => $this->whenLoaded('currentAnimator', function() {
                if (!$this->currentAnimator || !$this->currentAnimator->user) return null;
                return [
                    'user' => [
                        'id' => $this->currentAnimator->user->id,
                        'name' => $this->currentAnimator->user->name,
                        'avatar_url' => $this->currentAnimator->user->avatar_url,
                    ],
                    'role' => $this->currentAnimator->role,
                ];
            }),
            'participants' => DecisionParticipantResource::collection($this->whenLoaded('participants')),
            'model' => $this->whenLoaded('decisionModel'),
            
            // Appended attributes from controllers
            'participation_stats' => $this->when(isset($this->participation_stats), $this->participation_stats),
            'user_status' => $this->when(isset($this->user_status), $this->user_status),
            'phase_participation_map' => $this->when(isset($this->phase_participation_map), $this->phase_participation_map),

            // Revision draft fields
            'revision_content' => $this->revision_content,
            'revision_attachment_ids' => $this->revision_attachment_ids,
            'revision_attachments' => AttachmentResource::collection($this->when(isset($this->revision_attachments), $this->revision_attachments)),
        ];
    }
}
