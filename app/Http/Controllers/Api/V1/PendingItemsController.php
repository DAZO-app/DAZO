<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CircleMemberRole;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Http\Controllers\Controller;
use App\Models\Decision;
use App\Models\Feedback;
use App\Traits\HasUserActionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingItemsController extends Controller
{
    use HasUserActionStatus;

    /**
     * Returns an array of pending items for the authenticated user, 
     * tailored to a specific phase.
     * Optimized: no N+1 queries, uses pure SQL for filtering.
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
        $pendingDecisionsIds = Decision::where('status', $phase)
            ->whereHas('circle.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', '!=', CircleMemberRole::OBSERVER->value);
            })
            ->whereDoesntHave('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('role', [
                      DecisionParticipantRole::AUTHOR->value,
                      DecisionParticipantRole::EXCLUDED->value,
                  ]);
            })
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

        // 2. Feedbacks needing reply (Author/Animator needs to reply to others, or others need to reply to Author/Animator)
        $feedbackTypes = $phase === 'clarification'
            ? [FeedbackType::CLARIFICATION->value]
            : [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value];
            
        $terminal = [
            FeedbackStatus::WITHDRAWN->value,
            FeedbackStatus::ACKNOWLEDGED->value,
            FeedbackStatus::TREATED->value,
        ];

        $pendingFeedbackDecisionIds = Feedback::whereIn('type', $feedbackTypes)
            ->whereNotIn('status', $terminal)
            ->whereHas('version.decision', fn($q) => $q->where('status', $phase))
            ->where(function ($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function ($q2) use ($user) {
                      $q2->where('user_id', $user->id)
                         ->whereIn('role', [
                             DecisionParticipantRole::AUTHOR->value,
                             DecisionParticipantRole::ANIMATOR->value,
                         ]);
                  });
            })
            ->whereHas('latestMessage', fn($q) => $q->where('author_id', '!=', $user->id))
            ->pluck('decision_version_id') // We'll map to decisions
            ->toArray();
            
        // Map version IDs to decision IDs if needed, but since we already joined in SQL it's better to just get decision IDs
        $pendingFeedbackDecisionIds = \App\Models\DecisionVersion::whereIn('id', $pendingFeedbackDecisionIds)
            ->pluck('decision_id')
            ->toArray();

        $decisionIds = array_unique(array_merge($pendingDecisionsIds, $pendingFeedbackDecisionIds));

        $fullDecisions = Decision::whereIn('id', $decisionIds)
            ->with(['circle', 'categories', 'currentVersion', 'author.user', 'decisionModel', 'participants.user'])
            ->get();
            
        $this->attachUserActionStatus($fullDecisions, $user->id);
        
        return response()->json(['decisions' => $fullDecisions]);
    }
}
