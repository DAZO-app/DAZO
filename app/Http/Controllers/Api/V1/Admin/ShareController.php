<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleInviteLink;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;

class ShareController extends Controller
{
    public function index(): JsonResponse
    {
        $inviteLinks = CircleInviteLink::with(['circle', 'creator'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($link) => [
                'id' => $link->id,
                'type' => 'invite_link',
                'circle_id' => $link->circle_id,
                'circle_name' => $link->circle?->name,
                'token' => $link->token,
                'url' => $link->generateUrl(),
                'role' => $link->role,
                'created_by_name' => $link->creator?->name,
                'expires_at' => $link->expires_at,
                'is_expired' => $link->isExpired(),
                'is_valid' => $link->isValid(),
                'use_count' => $link->use_count,
                'max_uses' => $link->max_uses,
                'created_at' => $link->created_at,
            ]);

        $emailInvitations = Invitation::with(['circle', 'inviter'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($inv) => [
                'id' => $inv->id,
                'type' => 'email_invitation',
                'circle_id' => $inv->circle_id,
                'circle_name' => $inv->circle?->name,
                'email' => $inv->email,
                'role' => $inv->role,
                'invited_by_name' => $inv->inviter?->name,
                'token' => $inv->token,
                'expires_at' => $inv->expires_at,
                'is_expired' => $inv->expires_at ? $inv->expires_at->isPast() : false,
                'used_by' => $inv->used_by,
                'created_at' => $inv->created_at,
            ]);

        return response()->json([
            'invite_links' => $inviteLinks,
            'email_invitations' => $emailInvitations,
        ]);
    }
}
