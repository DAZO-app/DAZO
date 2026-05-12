<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CircleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'archived_at' => $this->archived_at,
            'is_archived' => $this->isArchived(),
            'is_sub_circle' => $this->isSubCircle(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members_count' => $this->whenCounted('members'),
            'decisions_count' => $this->whenCounted('decisions'),
            'children_count' => $this->whenCounted('children'),
            'members' => CircleMemberResource::collection($this->whenLoaded('members')),
            'children' => CircleResource::collection($this->whenLoaded('children')),
            'active_children' => CircleResource::collection($this->whenLoaded('activeChildren')),
            'archived_children' => CircleResource::collection($this->whenLoaded('archivedChildren')),
            'parent' => new CircleResource($this->whenLoaded('parent')),
            'invite_link' => $this->when($this->relationLoaded('inviteLink') && $this->inviteLink, function () {
                return [
                    'id' => $this->inviteLink->id,
                    'token' => $this->inviteLink->token,
                    'url' => $this->inviteLink->generateUrl(),
                    'role' => $this->inviteLink->role,
                    'expires_at' => $this->inviteLink->expires_at,
                    'is_expired' => $this->inviteLink->isExpired(),
                    'is_valid' => $this->inviteLink->isValid(),
                    'use_count' => $this->inviteLink->use_count,
                    'max_uses' => $this->inviteLink->max_uses,
                    'created_at' => $this->inviteLink->created_at,
                ];
            }),
            'invitations' => $this->when($this->relationLoaded('invitations'), function () {
                return $this->invitations->map(fn ($inv) => [
                    'id' => $inv->id,
                    'email' => $inv->email,
                    'role' => $inv->role,
                    'expires_at' => $inv->expires_at,
                    'created_at' => $inv->created_at,
                ]);
            }),
        ];
    }
}
