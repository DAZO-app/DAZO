<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DecisionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Decision\TransitionDecisionRequest;
use App\Models\Decision;
use App\Services\DecisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DecisionTransitionController extends Controller
{
    public function __construct(private DecisionService $decisionService)
    {
    }

    public function transition(TransitionDecisionRequest $request, string $decision_id): JsonResponse
    {
        $decision = Decision::findOrFail($decision_id);
        
        // Uniquement lecture nécessaire, le service vérifie les droits actifs
        if ($request->user()->cannot('view', $decision)) {
            abort(403);
        }

        $decision = $this->decisionService->transition(
            $decision, 
            $request->to, 
            $request->user(),
            false,
            $request->boolean('notify', true),
            $request->boolean('is_meeting', false)
        );

        return response()->json([
            'message' => "Décision passée au statut : {$request->to}",
            'decision' => $decision->fresh()
        ]);
    }

    public function abandon(Request $request, string $decision_id): JsonResponse
    {
        $decision = Decision::findOrFail($decision_id);
        
        if ($request->user()->cannot('view', $decision)) {
            abort(403);
        }

        $decision = $this->decisionService->transition(
            $decision, 
            DecisionStatus::ABANDONED->value, 
            $request->user(),
            false,
            $request->boolean('notify', true),
            $request->boolean('is_meeting', false)
        );

        return response()->json([
            'message' => 'La décision a été abandonnée avec succès.',
            'decision' => $decision->fresh()
        ]);
    }
}
