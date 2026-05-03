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

    public function extend(Request $request, string $decision_id): JsonResponse
    {
        $decision = Decision::findOrFail($decision_id);
        
        $user = $request->user();
        $isAuthorOrAnimator = $decision->participants()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                \App\Enums\DecisionParticipantRole::AUTHOR->value,
                \App\Enums\DecisionParticipantRole::ANIMATOR->value,
            ])->exists();

        if (!$isAuthorOrAnimator && $user->role->value !== \App\Enums\UserRole::ADMIN->value && $user->role->value !== \App\Enums\UserRole::SUPERADMIN->value) {
            abort(403, "Seul le porteur ou l'animateur peut prolonger la décision.");
        }

        $decision = $this->decisionService->extendDeadline($decision);

        return response()->json([
            'message' => 'La phase a été prolongée avec succès.',
            'decision' => $decision->fresh()
        ]);
    }

    /**
     * Rollback to a previous phase (meeting mode undo).
     * Accepts the previous status from the frontend action history.
     */
    public function rollbackPhase(Request $request, string $decision_id): JsonResponse
    {
        $request->validate([
            'previous_status' => 'required|string',
        ]);

        $decision = Decision::findOrFail($decision_id);
        
        $user = $request->user();
        $isAuthorOrAnimator = $decision->participants()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                \App\Enums\DecisionParticipantRole::AUTHOR->value,
                \App\Enums\DecisionParticipantRole::ANIMATOR->value,
            ])->exists();

        if (!$isAuthorOrAnimator && $user->role->value !== \App\Enums\UserRole::ADMIN->value && $user->role->value !== \App\Enums\UserRole::SUPERADMIN->value) {
            abort(403, "Seul le porteur ou l'animateur peut annuler une transition de phase.");
        }

        $previousStatus = $request->previous_status;
        
        // Validate the status is a known one
        $validStatuses = ['clarification', 'reaction', 'objection', 'revision', 'draft', 'suspended'];
        if (!in_array($previousStatus, $validStatuses)) {
            return response()->json(['message' => 'Statut cible invalide pour le rollback.'], 422);
        }

        $decision->status = $previousStatus;
        $decision->current_deadline = $this->decisionService->recalculateDeadline($previousStatus);
        $decision->reminder_sent = false;
        $decision->save();

        return response()->json([
            'message' => "Phase restaurée : {$previousStatus}",
            'decision' => $decision->fresh()
        ]);
    }
}
