<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\User;
use App\Models\Invitation;
use App\Models\CircleInviteLink;
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
        $query = Circle::with(['members.user', 'invitations', 'inviteLink', 'activeChildren.members.user', 'archivedChildren.members.user'])
            ->withCount(['children', 'members']);

        if ($request->filled('search')) {
            $searchTerm = '%' . strtolower($request->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('parent_id')) {
            $query->where(function($q) use ($request) {
                $q->where('id', $request->parent_id)
                  ->orWhere('parent_id', $request->parent_id);
            });
        } elseif (!$request->filled('search')) {
            $query->topLevel();
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
            'parent_id' => 'nullable|uuid|exists:circles,id',
        ]);

        // Validate depth: parent must be a top-level circle
        if (!empty($validated['parent_id'])) {
            $parent = Circle::findOrFail($validated['parent_id']);
            if ($parent->parent_id !== null) {
                return response()->json([
                    'message' => 'Un sous-cercle ne peut pas avoir de sous-cercle.'
                ], 422);
            }
        }

        $circle = $this->circleService->createCircle($validated, $request->user());

        // If sub-circle, copy members from parent
        if (!empty($validated['parent_id'])) {
            $parent = Circle::with('members')->findOrFail($validated['parent_id']);
            foreach ($parent->members as $member) {
                // Don't duplicate if already added (e.g. the creator)
                if (!$circle->members()->where('user_id', $member->user_id)->exists()) {
                    $circle->members()->create([
                        'user_id' => $member->user_id,
                        'role' => $member->role,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Cercle créé.',
            'circle' => new CircleResource($circle->load(['members.user', 'activeChildren.members.user', 'archivedChildren.members.user']))
        ], 201);
    }

    public function show(Circle $circle): CircleResource
    {
        return new CircleResource($circle->load([
            'members.user', 'invitations', 'inviteLink',
            'activeChildren.members.user', 'archivedChildren.members.user', 'parent'
        ]));
    }

    public function update(Request $request, Circle $circle): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:open,closed,observer_open',
            'parent_id' => 'nullable|uuid|exists:circles,id',
        ]);

        // Prevent circular dependency or deep nesting
        if (!empty($validated['parent_id'])) {
            if ($validated['parent_id'] === $circle->id) {
                return response()->json(['message' => 'Un cercle ne peut pas être son propre parent.'], 422);
            }
            $parent = Circle::findOrFail($validated['parent_id']);
            if ($parent->parent_id !== null) {
                return response()->json(['message' => 'Un sous-cercle ne peut pas avoir de sous-cercle.'], 422);
            }
            // If the circle already has children, it cannot become a sub-circle
            if ($circle->children()->exists()) {
                return response()->json(['message' => 'Ce cercle possède déjà des sous-cercles et ne peut donc pas devenir lui-même un sous-cercle.'], 422);
            }
        }

        $circle->update($validated);

        return response()->json([
            'message' => 'Cercle mis à jour.',
            'circle' => new CircleResource($circle->fresh(['members.user', 'activeChildren.members', 'archivedChildren.members']))
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

    // ─── Archive / Unarchive ─────────────────────────────

    public function archiveChild(Circle $circle): JsonResponse
    {
        if (!$circle->isSubCircle()) {
            return response()->json(['message' => 'Seul un sous-cercle peut être archivé.'], 422);
        }

        $circle->update(['archived_at' => now()]);

        return response()->json([
            'message' => 'Sous-cercle archivé.',
            'circle' => new CircleResource($circle->fresh('members.user'))
        ]);
    }

    public function unarchiveChild(Circle $circle): JsonResponse
    {
        $circle->update(['archived_at' => null]);

        return response()->json([
            'message' => 'Sous-cercle restauré.',
            'circle' => new CircleResource($circle->fresh('members.user'))
        ]);
    }

    // ─── Invite Link ─────────────────────────────────────

    public function createInviteLink(Request $request, Circle $circle): JsonResponse
    {
        // Delete existing link if any
        $circle->inviteLink()?->delete();

        $link = CircleInviteLink::create([
            'circle_id' => $circle->id,
            'token' => Str::random(64),
            'created_by' => $request->user()->id,
            'role' => 'member',
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'message' => 'Lien d\'invitation créé.',
            'invite_link' => [
                'id' => $link->id,
                'token' => $link->token,
                'url' => $link->generateUrl(),
                'role' => $link->role,
                'expires_at' => $link->expires_at,
                'use_count' => $link->use_count,
            ]
        ]);
    }

    public function deleteInviteLink(Circle $circle): JsonResponse
    {
        CircleInviteLink::where('circle_id', $circle->id)->delete();

        return response()->json(['message' => 'Lien d\'invitation révoqué.']);
    }

    // ─── Members ─────────────────────────────────────────

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
            'circle' => new CircleResource($circle->fresh(['members.user', 'invitations']))
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
            'circle' => new CircleResource($circle->fresh('members.user'))
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
            'circle' => new CircleResource($circle->fresh(['members.user', 'invitations']))
        ]);
    }
}
