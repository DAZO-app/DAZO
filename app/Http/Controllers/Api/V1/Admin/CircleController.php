<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\User;
use App\Models\Invitation;
use App\Enums\CircleMemberRole;
use App\Services\CircleService;
use App\Mail\CircleInvitationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Http\Resources\V1\CircleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CircleController extends Controller
{
    public function __construct(private CircleService $circleService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Circle::with(['members.user', 'invitations']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->integer('per_page', 20);
        $perPage = min(max($perPage, 5), 100);

        $circles = $query->orderBy('name')->paginate($perPage);

        return CircleResource::collection($circles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:open,closed,observer_open',
        ]);

        $circle = $this->circleService->createCircle($validated, $request->user());

        return response()->json([
            'message' => 'Cercle créé.',
            'circle' => $circle->load('members.user')
        ], 201);
    }

    public function show(Circle $circle): CircleResource
    {
        return new CircleResource($circle->load(['members.user', 'invitations']));
    }

    public function update(Request $request, Circle $circle): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:open,closed,observer_open',
        ]);

        $circle->update($validated);

        return response()->json([
            'message' => 'Cercle mis à jour.',
            'circle' => $circle->fresh('members.user')
        ]);
    }

    public function destroy(Circle $circle): JsonResponse
    {
        try {
            $circle->delete();
            return response()->json(['message' => 'Cercle supprimé.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Impossible de supprimer ce cercle (des données y sont liées).'], 403);
        }
    }

    public function addMember(Request $request, Circle $circle): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'nullable|exists:users,id',
            'emails' => 'nullable|string',
            'role' => ['required', Rule::in(['member', 'animator', 'observer'])],
        ]);

        $addedCount = 0;
        $invitedCount = 0;

        // 1. Process IDs
        if (!empty($validated['user_ids'])) {
            foreach ($validated['user_ids'] as $userId) {
                if ($userId && !$circle->members()->where('user_id', $userId)->exists()) {
                    $circle->members()->create([
                        'user_id' => $userId,
                        'role' => $validated['role'],
                    ]);
                    $addedCount++;
                }
            }
        }

        // 2. Process Emails
        if (!empty($validated['emails'])) {
            $emails = array_map('trim', explode(';', $validated['emails']));
            foreach ($emails as $email) {
                if (empty($email)) continue;
                
                // Check if user exists
                $user = User::where('email', $email)->first();
                if ($user) {
                    if (!$circle->members()->where('user_id', $user->id)->exists()) {
                        $circle->members()->create([
                            'user_id' => $user->id,
                            'role' => $validated['role'],
                        ]);
                        $addedCount++;
                    }
                } else {
                    // Create invitation
                    $invitation = Invitation::updateOrCreate(
                        ['circle_id' => $circle->id, 'email' => $email],
                        [
                            'inviter_id' => $request->user()->id,
                            'role' => $validated['role'],
                            'token' => Str::random(60),
                            'expires_at' => now()->addDays(7),
                        ]
                    );

                    Mail::to($email)->send(new CircleInvitationMail($invitation, $circle));
                    $invitedCount++;
                }
            }
        }

        return response()->json([
            'message' => "{$addedCount} membre(s) ajouté(s), {$invitedCount} invitation(s) envoyée(s).",
            'circle' => $circle->fresh(['members.user', 'invitations'])
        ]);
    }

    public function resendInvitation(Circle $circle, Invitation $invitation): JsonResponse
    {
        if ($invitation->circle_id !== $circle->id) abort(404);

        $invitation->update([
            'token' => Str::random(60),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->send(new CircleInvitationMail($invitation, $circle));

        return response()->json(['message' => 'Invitation renvoyée.']);
    }

    public function removeInvitation(Circle $circle, Invitation $invitation): JsonResponse
    {
        if ($invitation->circle_id !== $circle->id) abort(404);
        $invitation->delete();
        return response()->json(['message' => 'Invitation annulée.']);
    }

    public function removeMember(Circle $circle, User $user): JsonResponse
    {
        $this->circleService->removeMember($circle, $user);

        return response()->json([
            'message' => 'Membre retiré.',
            'circle' => $circle->fresh('members.user')
        ]);
    }

    public function updateMemberRole(Request $request, Circle $circle, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['member', 'animator', 'observer'])],
        ]);

        $circle->members()->where('user_id', $user->id)->update([
            'role' => $validated['role']
        ]);

        return response()->json([
            'message' => 'Rôle mis à jour.',
            'circle' => $circle->fresh(['members.user', 'invitations'])
        ]);
    }
}
