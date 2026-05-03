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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingCountsController extends Controller
{
    /**
     * Returns counts of items requiring the authenticated user's action.
     * Fully optimized using SQL queries (no in-memory filtering).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Base query for decisions where user is an eligible member
        $eligibleQuery = Decision::whereHas('circle.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', '!=', CircleMemberRole::OBSERVER->value);
            })
            ->whereDoesntHave('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('role', [
                      DecisionParticipantRole::AUTHOR->value,
                      DecisionParticipantRole::EXCLUDED->value,
                  ]);
            });

        $terminal = [
            FeedbackStatus::WITHDRAWN->value,
            FeedbackStatus::ACKNOWLEDGED->value,
            FeedbackStatus::REJECTED->value,
            FeedbackStatus::TREATED->value,
        ];

        // ──────────────────────────────────────────────────────────────────
        // 1. CLARIFICATIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseClari = DecisionStatus::CLARIFICATION->getPhaseConfig();
        
        // Decisions pending initial action (feedback/consent)
        $clarDecisionsCount = (clone $eligibleQuery)
            ->where('status', DecisionStatus::CLARIFICATION->value)
            ->whereDoesntHave('currentVersion.feedbacks', fn($q) => $q->where('author_id', $user->id)->whereIn('type', $phaseClari['feedback_types']))
            ->whereDoesntHave('currentVersion.consents', fn($q) => $q->where('user_id', $user->id)->whereIn('signal', $phaseClari['consent_signals']))
            ->count();

        // Thread replies pending
        $clarThreadPending = Feedback::where('type', FeedbackType::CLARIFICATION->value)
            ->whereNotIn('status', $terminal)
            ->whereHas('version.decision', fn($q) => $q->where('status', DecisionStatus::CLARIFICATION->value))
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
            ->count();

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

        // Thread replies pending for objections/suggestions
        $objThreadPending = Feedback::whereIn('type', [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value])
            ->whereNotIn('status', $terminal)
            ->whereHas('version.decision', fn($q) => $q->where('status', DecisionStatus::OBJECTION->value))
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
            ->count();

        return response()->json([
            'clarifications' => $clarDecisionsCount + $clarThreadPending,
            'reactions'      => $reactionPending,
            'objections'     => $objectionPending + $objThreadPending,
        ]);
    }
}
