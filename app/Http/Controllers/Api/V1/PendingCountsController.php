<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingCountsController extends Controller
{
    /**
     * Returns counts of items requiring the authenticated user's action.
     * 
     * - clarifications: Decisions in 'clarification' phase where the user hasn't posted a feedback OR consent yet
     * - reactions:      Decisions in 'reaction' phase where the user hasn't posted a consent ('no_reaction') yet  
     * - objections:     Open feedbacks in 'objection' phase where the user's action is needed (needs a reply in their own ticket,
     *                   or they haven't voted yet)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // --- Decisions where user is an eligible member (not author, not excluded, not observer) ---
        $eligibleDecisionIds = Decision::whereHas('circle.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value);
            })
            ->whereDoesntHave('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('role', [
                      \App\Enums\DecisionParticipantRole::AUTHOR->value,
                      \App\Enums\DecisionParticipantRole::EXCLUDED->value,
                  ]);
            })
            ->pluck('id');

        // 1. CLARIFICATIONS: user must either post a clarification OR say "it's clear" (no_questions consent)
        $clarificationDecisionIds = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', \App\Enums\DecisionStatus::CLARIFICATION->value)
            ->pluck('id', 'id'); 

        $clarPending = 0;
        foreach ($clarificationDecisionIds as $dId) {
            $decision = Decision::find($dId);
            $v = $decision?->currentVersion;
            if (!$v) continue;

            $hasActedInPhase = \App\Models\Feedback::where('decision_version_id', $v->id)
                ->where('author_id', $user->id)
                ->where('type', \App\Enums\FeedbackType::CLARIFICATION->value)
                ->exists()
                || \App\Models\Consent::where('decision_version_id', $v->id)
                    ->where('user_id', $user->id)
                    ->where('signal', \App\Enums\ConsentSignal::NO_QUESTIONS->value)
                    ->exists();

            if (!$hasActedInPhase) $clarPending++;
        }
        
        // Count clarification tickets needing reply (author of ticket or decision creator)
        $clarThreadPending = Feedback::where('type', \App\Enums\FeedbackType::CLARIFICATION->value)
            ->whereNotIn('status', [\App\Enums\FeedbackStatus::ACKNOWLEDGED->value, \App\Enums\FeedbackStatus::WITHDRAWN->value])
            ->whereHas('version.decision', fn($q) => $q->where('status', \App\Enums\DecisionStatus::CLARIFICATION->value))
            ->where(function ($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function ($q2) use ($user) {
                      $q2->where('user_id', $user->id)
                         ->whereIn('role', [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value]);
                  });
            })
            ->get()
            ->filter(fn($fb) => ($last = $fb->messages->last()) && $last->author_id !== $user->id)
            ->count();

        // 2. REACTIONS: user hasn't posted a consent ('no_reaction') OR a feedback ('reaction') yet
        $reactionPending = 0;
        $reactionDecisionIds = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', \App\Enums\DecisionStatus::REACTION->value)
            ->get();

        foreach ($reactionDecisionIds as $d) {
            $v = $d->currentVersion;
            if (!$v) continue;

            $hasActedInPhase = \App\Models\Consent::where('decision_version_id', $v->id)
                ->where('user_id', $user->id)
                ->where('signal', \App\Enums\ConsentSignal::NO_REACTION->value)
                ->exists()
                || \App\Models\Feedback::where('decision_version_id', $v->id)
                    ->where('author_id', $user->id)
                    ->where('type', \App\Enums\FeedbackType::REACTION->value)
                    ->exists();

            if (!$hasActedInPhase) $reactionPending++;
        }

        // 3. OBJECTIONS: user hasn't voted (no_objection / abstention) AND has no active ticket in objection phase
        $objectionPending = 0;
        $objectionDecisionIds = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', \App\Enums\DecisionStatus::OBJECTION->value)
            ->get();

        foreach ($objectionDecisionIds as $d) {
            $v = $d->currentVersion;
            if (!$v) continue;

            $hasActedInPhase = \App\Models\Consent::where('decision_version_id', $v->id)
                ->where('user_id', $user->id)
                ->whereIn('signal', [\App\Enums\ConsentSignal::NO_OBJECTION->value, \App\Enums\ConsentSignal::ABSTENTION->value])
                ->exists()
                || \App\Models\Feedback::where('decision_version_id', $v->id)
                    ->where('author_id', $user->id)
                    ->whereIn('type', [\App\Enums\FeedbackType::OBJECTION->value, \App\Enums\FeedbackType::SUGGESTION->value])
                    ->exists();

            if (!$hasActedInPhase) $objectionPending++;
        }

        // Objection threads needing reply
        $objThreadPending = Feedback::whereIn('type', [\App\Enums\FeedbackType::OBJECTION->value, \App\Enums\FeedbackType::SUGGESTION->value])
            ->whereNotIn('status', [\App\Enums\FeedbackStatus::WITHDRAWN->value, \App\Enums\FeedbackStatus::ACKNOWLEDGED->value])
            ->whereHas('version.decision', fn($q) => $q->where('status', \App\Enums\DecisionStatus::OBJECTION->value))
            ->where(function ($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function ($q2) use ($user) {
                      $q2->where('user_id', $user->id)
                         ->whereIn('role', [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value]);
                  });
            })
            ->get()
            ->filter(fn($fb) => ($last = $fb->messages->last()) && $last->author_id !== $user->id)
            ->count();

        return response()->json([
            'clarifications' => $clarPending + $clarThreadPending,
            'reactions'      => $reactionPending,
            'objections'     => $objectionPending + $objThreadPending,
        ]);
    }
}
