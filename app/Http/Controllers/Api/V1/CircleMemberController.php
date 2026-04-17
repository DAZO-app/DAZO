<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CircleType;
use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\CircleMember;
use App\Services\CircleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CircleMemberController extends Controller
{
    public function __construct(private CircleService $circleService)
    {
    }

    public function index(Request $request, Circle $circle): JsonResponse
    {
        if ($request->user()->cannot('view', $circle)) {
            abort(403, "Accès refusé.");
        }

        return response()->json([
            'members' => $circle->members()->with('user')->get()
        ]);
    }

    public function join(Request $request, Circle $circle): JsonResponse
    {
        $user = $request->user();

        if ($circle->type->value === CircleType::CLOSED->value) {
            return response()->json(['message' => 'Ce cercle est fermé.'], 403);
        }

        if ($circle->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà membre.'], 400);
        }

        $role = ($circle->type->value === CircleType::OBSERVER_OPEN->value) 
            ? \App\Enums\CircleMemberRole::OBSERVER->value 
            : \App\Enums\CircleMemberRole::MEMBER->value;

        // Si OBSERVER_OPEN, l'utilisateur rejoint par défaut en tant qu'observateur.
        $circle->members()->create([
            'user_id' => $user->id,
            'role' => $role,
        ]);

        return response()->json(['message' => 'Cercle rejoint avec succès.'], 201);
    }

    public function leave(Request $request, Circle $circle): JsonResponse
    {
        $user = $request->user();

        // Check if member
        if (! $circle->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => "Vous n'êtes pas membre de ce cercle."], 400);
        }

        // Check active decisions (TODO for Bloc 3)
        // $hasActiveDecisions = Decision::where('circle_id', $circle->id)
        //    ->whereHas('author', fn($q) => $q->where('user_id', $user->id))
        //    ->whereIn('status', ['draft', 'clarification', 'reaction', 'revision', 'objection'])
        //    ->exists();
        
        $this->circleService->removeMember($circle, $user);

        return response()->json(['message' => 'Vous avez quitté le cercle.']);
    }

    public function destroy(Request $request, Circle $circle, string $userId): JsonResponse
    {
        if ($request->user()->cannot('update', $circle)) {
            abort(403, "Seul un animateur peut exclure un membre.");
        }

        $memberUser = \App\Models\User::findOrFail($userId);

        $this->circleService->removeMember($circle, $memberUser);

        return response()->json(['message' => 'Membre exclu avec succès.']);
    }
}
