<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Circle\CreateCircleRequest;
use App\Http\Requests\Circle\UpdateCircleRequest;
use App\Models\Circle;
use App\Services\CircleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CircleController extends Controller
{
    public function __construct(private CircleService $circleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        // Retourne les cercles dont l'utilisateur est membre
        $user = $request->user();

        $circles = Circle::whereHas('members', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return response()->json(['circles' => $circles]);
    }

    public function store(CreateCircleRequest $request): JsonResponse
    {
        $circle = $this->circleService->createCircle($request->validated(), $request->user());

        return response()->json([
            'message' => 'Cercle créé avec succès.',
            'circle' => $circle->load('members.user'),
        ], 201);
    }

    public function show(Request $request, Circle $circle): JsonResponse
    {
        if ($request->user()->cannot('view', $circle)) {
            abort(403, "Vous n'avez pas accès à ce cercle.");
        }

        return response()->json([
            'circle' => $circle->load('members.user')
        ]);
    }

    public function update(UpdateCircleRequest $request, Circle $circle): JsonResponse
    {
        $circle->update($request->validated());

        return response()->json([
            'message' => 'Cercle mis à jour.',
            'circle' => $circle->fresh(),
        ]);
    }

    public function destroy(Request $request, Circle $circle): JsonResponse
    {
        if ($request->user()->cannot('delete', $circle)) {
            abort(403, "Vous ne pouvez pas supprimer ce cercle.");
        }

        try {
            $circle->delete();
            return response()->json(['message' => 'Cercle supprimé.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Impossible de supprimer ce cercle (des décisions y sont liées).'], 403);
        }
    }
}
