<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Decision\CreateDecisionRequest;
use App\Models\Circle;
use App\Models\Decision;
use App\Services\DecisionService;
use Illuminate\Http\JsonResponse;

class DecisionController extends Controller
{
    public function __construct(private DecisionService $decisionService)
    {
    }

    public function mine(): JsonResponse
    {
        $decisions = Decision::whereHas('circle.members', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['circle', 'currentVersion', 'author.user', 'decisionModel'])
            ->latest()
            ->get();

        return response()->json(['decisions' => $decisions]);
    }

    public function index(string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        $this->authorize('view', $circle);

        $decisions = Decision::where('circle_id', $circle->id)
            ->with(['circle', 'currentVersion', 'author.user', 'decisionModel'])
            ->get();

        return response()->json(['decisions' => $decisions]);
    }

    public function store(CreateDecisionRequest $request, string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        
        $decision = $this->decisionService->createDecision($request->validated(), $request->user(), $circle);

        return response()->json([
            'message' => 'Décision initiée.',
            'decision' => $decision->load(['currentVersion', 'participants.user']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('view', $decision);

        return response()->json([
            'decision' => $decision->load([
                'currentVersion', 
                'participants.user', 
                'circle', 
                'category', 
                'decisionModel'
            ])
        ]);
    }
}
