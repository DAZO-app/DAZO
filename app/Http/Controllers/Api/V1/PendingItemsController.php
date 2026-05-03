<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use App\Http\Controllers\Controller;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Services\DecisionParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingItemsController extends Controller
{

    public function __construct(private DecisionParticipationService $participationService)
    {
    }

    /**
     * Returns an array of pending items for the authenticated user, 
     * tailored to a specific phase.
     * Optimized: uses DecisionParticipationService for pure SQL filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $phase = $request->string('phase')->toString(); // 'clarification' | 'reaction' | 'objection'

        $allowedPhases = ['clarification', 'reaction', 'objection'];
        if (!in_array($phase, $allowedPhases)) {
            return response()->json(['decisions' => []]);
        }

        $decisionStatus = DecisionStatus::from($phase);
        $phaseConfig = $decisionStatus->getPhaseConfig();

        // 1. Decisions needing initial action (feedback/consent)
        $pendingDecisionsIds = $this->participationService->getEligibleDecisionsQuery($user)
            ->where('status', $phase)
            ->whereDoesntHave('currentVersion.feedbacks', function ($q) use ($user, $phaseConfig) {
                $q->where('author_id', $user->id)
                  ->whereIn('type', $phaseConfig['feedback_types']);
            })
            ->whereDoesntHave('currentVersion.consents', function ($q) use ($user, $phaseConfig) {
                $q->where('user_id', $user->id)
                  ->whereIn('signal', $phaseConfig['consent_signals']);
            })
            ->pluck('id')
            ->toArray();

        // 2. Feedbacks needing reply
        $feedbackTypes = $phase === 'clarification'
            ? [FeedbackType::CLARIFICATION->value]
            : [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value];
            
        $pendingFeedbackDecisionIds = $this->participationService->getPendingFeedbacksQuery($user, $feedbackTypes, $phase)
            ->pluck('decision_version_id')
            ->toArray();
            
        $pendingFeedbackDecisionIds = DecisionVersion::whereIn('id', $pendingFeedbackDecisionIds)
            ->pluck('decision_id')
            ->toArray();

        $decisionIds = array_unique(array_merge($pendingDecisionsIds, $pendingFeedbackDecisionIds));

        $fullDecisions = Decision::whereIn('id', $decisionIds)
            ->with(['circle', 'categories', 'currentVersion', 'author.user', 'decisionModel', 'participants.user'])
            ->get();
            
        $this->participationService->attachUserActionStatus($fullDecisions, $user->id);
        
        return response()->json(['decisions' => $fullDecisions]);
    }
}
