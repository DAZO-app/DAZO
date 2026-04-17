<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitation\CreateInvitationRequest;
use App\Models\Circle;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function store(CreateInvitationRequest $request): JsonResponse
    {
        $invitation = Invitation::create([
            'circle_id' => $request->circle_id,
            'inviter_id' => $request->user()->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(60),
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: Déclencher Notification / Email (Via Event)
        // event(new \App\Events\InvitationSent($invitation));

        return response()->json([
            'message' => 'Invitation envoyée.',
            'invitation' => $invitation,
            'link' => url("/invitations/{$invitation->token}/accept"), // Frontend link usually
        ], 201);
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->expires_at->isPast()) {
            return response()->json(['message' => 'Invitation expirée.'], 400);
        }

        if ($invitation->used_by !== null) {
            return response()->json(['message' => 'Invitation déjà utilisée.'], 400);
        }

        $user = $request->user();

        // Check if correct user
        if ($user->email !== $invitation->email) {
             return response()->json(['message' => 'Cette invitation ne vous est pas adressée.'], 403);
        }

        $circle = $invitation->circle;

        if (! $circle->members()->where('user_id', $user->id)->exists()) {
            $circle->members()->create([
                'user_id' => $user->id,
                'role' => $invitation->role,
            ]);
        }

        $invitation->update(['used_by' => $user->id]);

        return response()->json([
            'message' => 'Invitation acceptée, vous avez rejoint le cercle.',
        ]);
    }
}
