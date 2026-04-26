<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
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
     * Optimized: no N+1 queries. Uses eager loading + in-memory collection processing.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // IDs des décisions où l'utilisateur est membre éligible (non auteur, non exclu, non observateur)
        $eligibleDecisionIds = Decision::whereHas('circle.members', function ($q) use ($user) {
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
            ->pluck('id');

        // ──────────────────────────────────────────────────────────────────
        // 1. CLARIFICATIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseClari = DecisionStatus::CLARIFICATION->getPhaseConfig();

        // Charger toutes les décisions en clarification avec leurs relations en UNE SEULE requête
        $clarDecisions = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', DecisionStatus::CLARIFICATION->value)
            ->with([
                'currentVersion.feedbacks' => fn($q) => $q
                    ->where('author_id', $user->id)
                    ->whereIn('type', $phaseClari['feedback_types']),
                'currentVersion.consents' => fn($q) => $q
                    ->where('user_id', $user->id)
                    ->whereIn('signal', $phaseClari['consent_signals']),
            ])
            ->get();

        $clarPending = $clarDecisions->filter(function ($d) {
            $v = $d->currentVersion;
            if (!$v) return false;
            return $v->feedbacks->isEmpty() && $v->consents->isEmpty();
        })->count();

        // Threads clarification nécessitant une réponse (eager loaded)
        $terminal = [
            FeedbackStatus::WITHDRAWN->value,
            FeedbackStatus::ACKNOWLEDGED->value,
            FeedbackStatus::REJECTED->value,
            FeedbackStatus::TREATED->value,
        ];

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
            ->with('messages')
            ->get()
            ->filter(fn($fb) => ($last = $fb->messages->sortByDesc('created_at')->first())
                && $last->author_id !== $user->id)
            ->count();

        // ──────────────────────────────────────────────────────────────────
        // 2. REACTIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseReact = DecisionStatus::REACTION->getPhaseConfig();

        $reactDecisions = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', DecisionStatus::REACTION->value)
            ->with([
                'currentVersion.feedbacks' => fn($q) => $q
                    ->where('author_id', $user->id)
                    ->whereIn('type', $phaseReact['feedback_types']),
                'currentVersion.consents' => fn($q) => $q
                    ->where('user_id', $user->id)
                    ->whereIn('signal', $phaseReact['consent_signals']),
            ])
            ->get();

        $reactionPending = $reactDecisions->filter(function ($d) {
            $v = $d->currentVersion;
            if (!$v) return false;
            return $v->feedbacks->isEmpty() && $v->consents->isEmpty();
        })->count();

        // ──────────────────────────────────────────────────────────────────
        // 3. OBJECTIONS
        // ──────────────────────────────────────────────────────────────────
        $phaseObj = DecisionStatus::OBJECTION->getPhaseConfig();

        $objDecisions = Decision::whereIn('id', $eligibleDecisionIds)
            ->where('status', DecisionStatus::OBJECTION->value)
            ->with([
                'currentVersion.feedbacks' => fn($q) => $q
                    ->where('author_id', $user->id)
                    ->whereIn('type', $phaseObj['feedback_types']),
                'currentVersion.consents' => fn($q) => $q
                    ->where('user_id', $user->id)
                    ->whereIn('signal', $phaseObj['consent_signals']),
            ])
            ->get();

        $objectionPending = $objDecisions->filter(function ($d) {
            $v = $d->currentVersion;
            if (!$v) return false;
            return $v->feedbacks->isEmpty() && $v->consents->isEmpty();
        })->count();

        // Threads objection nécessitant une réponse
        $objThreadPending = Feedback::whereIn('type', $phaseObj['feedback_types'])
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
            ->with('messages')
            ->get()
            ->filter(fn($fb) => ($last = $fb->messages->sortByDesc('created_at')->first())
                && $last->author_id !== $user->id)
            ->count();

        return response()->json([
            'clarifications' => $clarPending + $clarThreadPending,
            'reactions'      => $reactionPending,
            'objections'     => $objectionPending + $objThreadPending,
        ]);
    }
}
