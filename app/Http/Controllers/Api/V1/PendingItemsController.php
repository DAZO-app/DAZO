<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingItemsController extends Controller
{
    /**
     * Returns an array of pending items for the authenticated user, 
     * tailored to a specific phase.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $phase = $request->string('phase')->toString(); // 'clarification' | 'reaction' | 'objection'

        $allowedPhases = ['clarification', 'reaction', 'objection'];
        if (!in_array($phase, $allowedPhases)) {
            return response()->json(['items' => []]);
        }

        $items = [];

        // ==== CASE 1: Decisions the user is a member of but hasn't acted on yet ====
        // (Applicable for all 3 phases - user must vote or post a feedback)
        $decisions = Decision::where('status', $phase)
            ->whereHas('circle.members', function ($q) use ($user) {
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
            ->with(['circle', 'currentVersion'])
            ->get();

        foreach ($decisions as $d) {
            $versionId = $d->currentVersion?->id;
            if (!$versionId) continue;

            $hasActed = Consent::where('decision_version_id', $versionId)->where('user_id', $user->id)->exists()
                || Feedback::where('decision_version_id', $versionId)->where('author_id', $user->id)->exists();

            if (!$hasActed) {
                $role = $d->participants()->where('user_id', $user->id)->first()?->role->value;
                if (!$role) {
                     $role = $d->circle->members()->where('user_id', $user->id)->first()?->role->value;
                }

                $items[] = [
                    'id'              => 'decision-' . $d->id,
                    'decision_id'     => $d->id,
                    'circle_name'     => $d->circle?->name,
                    'decision_title'  => $d->title,
                    'version_number'  => $d->currentVersion?->version_number,
                    'my_role'         => $role ?? 'participant',
                    'needs_reply'     => false,
                    'last_message'    => null,
                    'last_message_author' => null,
                ];
            }
        }

        // ==== CASE 2: Open feedback threads where the user needs to reply ====
        if (in_array($phase, ['clarification', 'objection'])) {
            $feedbackTypes = $phase === 'clarification'
                ? [\App\Enums\FeedbackType::CLARIFICATION->value]
                : [\App\Enums\FeedbackType::OBJECTION->value, \App\Enums\FeedbackType::SUGGESTION->value];

            $feedbacks = Feedback::whereIn('type', $feedbackTypes)
                ->whereNotIn('status', [\App\Enums\FeedbackStatus::WITHDRAWN->value, \App\Enums\FeedbackStatus::ACKNOWLEDGED->value, \App\Enums\FeedbackStatus::TREATED->value])
                ->whereHas('version.decision', function ($q) use ($phase) {
                    $q->where('status', $phase);
                })
                ->where(function ($q) use ($user) {
                    $q->where('author_id', $user->id)
                      ->orWhereHas('version.decision.participants', function ($q2) use ($user) {
                          $q2->where('user_id', $user->id)
                             ->whereIn('role', [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value]);
                      });
                })
                ->with(['author', 'version.decision.circle', 'messages.author', 'version.decision.participants'])
                ->get();

            foreach ($feedbacks as $fb) {
                $messages = $fb->messages?->sortBy('created_at') ?? collect();
                $lastMsg = $messages->last();
                if (!$lastMsg) continue;

                if ((string) $lastMsg->author_id === (string) $user->id) continue;

                $decision = $fb->version?->decision;
                if (!$decision) continue;

                $role = $decision->participants->where('user_id', $user->id)->first()?->role->value;
                if (!$role) {
                    $role = $decision->circle->members()->where('user_id', $user->id)->first()?->role->value;
                }

                $items[] = [
                    'id'                  => 'thread-' . $fb->id,
                    'decision_id'         => $decision->id,
                    'circle_name'         => $decision->circle?->name,
                    'decision_title'      => $decision->title,
                    'version_number'      => $fb->version?->version_number ?? 1,
                    'my_role'             => $role ?? 'participant',
                    'needs_reply'         => true,
                    'last_message'        => mb_strimwidth($lastMsg->content, 0, 80, '...'),
                    'last_message_author' => $lastMsg->author?->name ?? 'Quelqu\'un',
                ];
            }
        }

        // Sort: needs_reply first, then by id desc
        usort($items, fn($a, $b) => ($b['needs_reply'] <=> $a['needs_reply']));

        return response()->json(['items' => $items]);
    }
}
