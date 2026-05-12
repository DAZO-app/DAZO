<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleInviteLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InviteLinkController extends Controller
{
    /**
     * Show invite link info (public, no auth required).
     */
    public function show(string $token): JsonResponse
    {
        $link = CircleInviteLink::where('token', $token)->firstOrFail();

        if ($link->isExpired()) {
            return response()->json([
                'message' => 'Ce lien d\'invitation a expiré.',
                'expired' => true,
            ], 410);
        }

        if (!$link->isValid()) {
            return response()->json([
                'message' => 'Ce lien d\'invitation n\'est plus valide.',
                'expired' => true,
            ], 410);
        }

        $circle = $link->circle;

        return response()->json([
            'circle' => [
                'id' => $circle->id,
                'name' => $circle->name,
                'description' => $circle->description,
                'type' => $circle->type,
                'members_count' => $circle->members()->count(),
            ],
            'role' => $link->role,
            'expires_at' => $link->expires_at,
        ]);
    }

    /**
     * Accept invite link (auth required).
     */
    public function accept(Request $request, string $token): JsonResponse
    {
        $link = CircleInviteLink::where('token', $token)->firstOrFail();

        if (!$link->isValid()) {
            return response()->json([
                'message' => 'Ce lien d\'invitation n\'est plus valide.',
            ], 410);
        }

        $user = $request->user();
        $circle = $link->circle;

        // Check if already a member
        if ($circle->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Vous êtes déjà membre de ce cercle.',
                'circle_id' => $circle->id,
                'already_member' => true,
            ]);
        }

        // Add as member
        $circle->members()->create([
            'user_id' => $user->id,
            'role' => $link->role,
        ]);

        // Increment use count
        $link->increment('use_count');

        return response()->json([
            'message' => "Vous avez rejoint le cercle « {$circle->name} ».",
            'circle_id' => $circle->id,
        ]);
    }
}
