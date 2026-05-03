<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use App\Http\Controllers\Controller;
use App\Services\DecisionParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingCountsController extends Controller
{
    public function __construct(private DecisionParticipationService $participationService)
    {
    }

    /**
     * Returns counts of items requiring the authenticated user's action.
     * Fully optimized using SQL queries via DecisionParticipationService.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Base query for decisions where user is an eligible member
        $eligibleQuery = $this->participationService->getEligibleDecisionsQuery($user);

        // ──────────────────────────────────────────────────────────────────
        // 1. CLARIFICATIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseClari = DecisionStatus::CLARIFICATION->getPhaseConfig();
        
        $clarDecisionsCount = (clone $eligibleQuery)
            ->where('status', DecisionStatus::CLARIFICATION->value)
            ->whereDoesntHave('currentVersion.feedbacks', fn($q) => $q->where('author_id', $user->id)->whereIn('type', $phaseClari['feedback_types']))
            ->whereDoesntHave('currentVersion.consents', fn($q) => $q->where('user_id', $user->id)->whereIn('signal', $phaseClari['consent_signals']))
            ->count();

        $clarThreadPending = $this->participationService->getPendingFeedbacksQuery(
                $user, 
                [FeedbackType::CLARIFICATION->value], 
                DecisionStatus::CLARIFICATION->value
            )->count();

        // ──────────────────────────────────────────────────────────────────
        // 2. REACTIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseReact = DecisionStatus::REACTION->getPhaseConfig();

        $reactionPending = (clone $eligibleQuery)
            ->where('status', DecisionStatus::REACTION->value)
            ->whereDoesntHave('currentVersion.feedbacks', fn($q) => $q->where('author_id', $user->id)->whereIn('type', $phaseReact['feedback_types']))
            ->whereDoesntHave('currentVersion.consents', fn($q) => $q->where('user_id', $user->id)->whereIn('signal', $phaseReact['consent_signals']))
            ->count();

        // ──────────────────────────────────────────────────────────────────
        // 3. OBJECTIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseObj = DecisionStatus::OBJECTION->getPhaseConfig();

        $objectionPending = (clone $eligibleQuery)
            ->where('status', DecisionStatus::OBJECTION->value)
            ->whereDoesntHave('currentVersion.feedbacks', fn($q) => $q->where('author_id', $user->id)->whereIn('type', $phaseObj['feedback_types']))
            ->whereDoesntHave('currentVersion.consents', fn($q) => $q->where('user_id', $user->id)->whereIn('signal', $phaseObj['consent_signals']))
            ->count();

        $objThreadPending = $this->participationService->getPendingFeedbacksQuery(
                $user, 
                [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value], 
                DecisionStatus::OBJECTION->value
            )->count();

        return response()->json([
            'clarifications' => $clarDecisionsCount + $clarThreadPending,
            'reactions'      => $reactionPending,
            'objections'     => $objectionPending + $objThreadPending,
        ]);
    }
}
