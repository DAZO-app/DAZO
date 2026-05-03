<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Decision;
use App\Models\Feedback;
use App\Enums\DecisionStatus;
use App\Enums\DecisionParticipantRole;
use App\Enums\CircleMemberRole;
use App\Enums\FeedbackStatus;
use App\Services\DecisionParticipationService;

class DashboardController extends Controller
{
    public function __construct(private DecisionParticipationService $participationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1a. Decisions where I am AUTHOR
        $authorDecisions = Decision::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('role', DecisionParticipantRole::AUTHOR->value);
        })->with(['circle', 'currentVersion.attachments', 'participants.user'])->get();

        // 1b. Decisions where I am ANIMATOR (but not author)
        $authorIds = $authorDecisions->pluck('id')->toArray();
        $animatorDecisions = Decision::whereNotIn('id', $authorIds)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', DecisionParticipantRole::ANIMATOR->value);
            })->with(['circle', 'currentVersion.attachments', 'participants.user'])->get();

        // Group by circle name
        $groupByCircle = function ($decisions) {
            $grouped = [];
            foreach ($decisions as $d) {
                $key = $d->circle ? $d->circle->name : 'Global';
                $grouped[$key][] = $d;
            }
            return $grouped;
        };

        // Attach action status (delegated to service)
        $this->participationService->attachUserActionStatus($authorDecisions, $user->id);
        $this->participationService->attachUserActionStatus($animatorDecisions, $user->id);

        $myDecisionsGrouped   = $groupByCircle($authorDecisions);
        $myAnimatedGrouped    = $groupByCircle($animatorDecisions);
        
        // 2. Circle decisions: member but not author/animator
        $myAllIds = array_merge($authorIds, $animatorDecisions->pluck('id')->toArray());
        $circleDecisions = Decision::whereNotIn('id', $myAllIds)
            ->whereHas('circle.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('role', '!=', CircleMemberRole::OBSERVER->value);
            })
            ->whereNotIn('status', [DecisionStatus::DRAFT->value, DecisionStatus::ADOPTED->value, DecisionStatus::ABANDONED->value])
            ->with(['circle', 'currentVersion.attachments', 'participants.user'])->get();

        $circleDecisionsGrouped = $groupByCircle($circleDecisions);
        $this->participationService->attachUserActionStatus($circleDecisions, $user->id);

        // 3. My Clarifications (active threads)
        $terminal = [FeedbackStatus::WITHDRAWN->value, FeedbackStatus::ACKNOWLEDGED->value, FeedbackStatus::REJECTED->value, FeedbackStatus::TREATED->value];
        $clarifications = Feedback::where('type', 'clarification')
            ->whereNotIn('status', $terminal)
            ->where(function($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function($q2) use ($user) {
                      $q2->where('user_id', $user->id)->whereIn('role', [DecisionParticipantRole::AUTHOR->value, DecisionParticipantRole::ANIMATOR->value]);
                  });
            })
            ->with(['author', 'version.decision.circle', 'version.decision.currentVersion.attachments', 'messages.author'])
            ->orderByDesc('created_at')->get();

        // 4. My Objections (active threads)
        $objections = Feedback::where('type', 'objection')
            ->whereNotIn('status', $terminal)
            ->where(function($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function($q2) use ($user) {
                      $q2->where('user_id', $user->id)->whereIn('role', [DecisionParticipantRole::AUTHOR->value, DecisionParticipantRole::ANIMATOR->value]);
                  });
            })
            ->with(['author', 'version.decision.circle', 'version.decision.currentVersion.attachments', 'messages.author'])
            ->orderByDesc('created_at')->get();

        // 5. STATS (Query optimized)
        $visibleQuery = Decision::where(function ($q) use ($user) {
            $q->whereHas('circle.members', function ($q2) use ($user) {
                $q2->where('user_id', $user->id);
            })->orWhereHas('participants', function ($q2) use ($user) {
                $q2->where('user_id', $user->id);
            });
        })->where(function ($q) use ($user) {
            $q->where('status', '!=', DecisionStatus::DRAFT->value)
              ->orWhereHas('participants', function ($q2) use ($user) {
                  $q2->where('user_id', $user->id)->whereIn('role', [DecisionParticipantRole::AUTHOR->value, DecisionParticipantRole::ANIMATOR->value]);
              });
        });

        $stats = [
            'total'        => $visibleQuery->count(),
            'as_author'    => $authorDecisions->count(),
            'as_animator'  => $animatorDecisions->count(),
            'draft'        => (clone $visibleQuery)->where('status', DecisionStatus::DRAFT->value)->count(),
            'in_progress'  => (clone $visibleQuery)->whereIn('status', [DecisionStatus::CLARIFICATION->value, DecisionStatus::REACTION->value, DecisionStatus::OBJECTION->value])->count(),
            'adopted'      => (clone $visibleQuery)->whereIn('status', [DecisionStatus::ADOPTED->value, DecisionStatus::ADOPTED_OVERRIDE->value])->count(),
            'abandoned'    => (clone $visibleQuery)->whereIn('status', [DecisionStatus::ABANDONED->value, DecisionStatus::DESERTED->value, DecisionStatus::LAPSED->value])->count(),
        ];

        // Cache categories for 1 hour
        $categories = \Illuminate\Support\Facades\Cache::remember('categories_all', 3600, function () {
            return \App\Models\Category::all();
        });

        return response()->json([
            'my_decisions'     => $myDecisionsGrouped,
            'my_animated'      => $myAnimatedGrouped,
            'circle_decisions' => $circleDecisionsGrouped,
            'my_clarifications'=> $clarifications,
            'my_objections'    => $objections,
            'stats'            => $stats,
            'categories'       => $categories,
        ]);
    }
}
