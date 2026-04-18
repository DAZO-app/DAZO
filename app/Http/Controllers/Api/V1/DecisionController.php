<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Decision\CreateDecisionRequest;
use App\Models\Circle;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\Feedback;
use App\Services\DecisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function __construct(private DecisionService $decisionService)
    {
    }

    public function mine(): JsonResponse
    {
        $decisions = Decision::whereHas('circle.members', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['circle', 'currentVersion', 'author.user', 'decisionModel', 'participants.user'])
            ->latest()
            ->get();

        return response()->json(['decisions' => $decisions]);
    }

    public function index(string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        $this->authorize('view', $circle);

        $decisions = Decision::where('circle_id', $circle->id)
            ->with(['circle', 'currentVersion', 'author.user', 'decisionModel', 'participants.user'])
            ->get();

        return response()->json(['decisions' => $decisions]);
    }

    public function store(CreateDecisionRequest $request, string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        
        $decision = $this->decisionService->createDecision($request->validated(), $request->user(), $circle);

        return response()->json([
            'message' => 'Décision initiée.',
            'decision' => $decision->load(['currentVersion', 'participants.user']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('view', $decision);

        // Participation Stats logic
        $circle = $decision->circle;
        $totalMembers = $circle->members()->where('role', '!=', CircleMemberRole::OBSERVER->value)->pluck('user_id')->toArray();
        $excludedOrManaging = $decision->participants()
            ->whereIn('role', [
                DecisionParticipantRole::EXCLUDED->value,
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value
            ])->pluck('user_id')->toArray();
        
        $eligible = array_diff($totalMembers, $excludedOrManaging);
        
        $v = $decision->currentVersion;
        $participated = 0;
        $status = $decision->status->value;
        $phaseFeedbackTypes = [];
        $phaseConsentSignals = [];

        if ($v && in_array($status, [DecisionStatus::CLARIFICATION->value, DecisionStatus::REACTION->value, DecisionStatus::OBJECTION->value], true)) {
            
            if ($status === DecisionStatus::CLARIFICATION->value) {
                $phaseFeedbackTypes = [FeedbackType::CLARIFICATION->value];
                $phaseConsentSignals = [ConsentSignal::NO_QUESTIONS->value];
            } elseif ($status === DecisionStatus::REACTION->value) {
                $phaseFeedbackTypes = [FeedbackType::REACTION->value];
                $phaseConsentSignals = [ConsentSignal::NO_REACTION->value];
            } elseif ($status === DecisionStatus::OBJECTION->value) {
                $phaseFeedbackTypes = [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value];
                $phaseConsentSignals = [ConsentSignal::NO_OBJECTION->value, ConsentSignal::ABSTENTION->value];
            }

            $feedbackAuthors = Feedback::where('decision_version_id', $v->id)
                ->whereIn('type', $phaseFeedbackTypes)
                ->pluck('author_id')->toArray();
                
            $consentAuthors = Consent::where('decision_version_id', $v->id)
                ->whereIn('signal', $phaseConsentSignals)
                ->pluck('user_id')->toArray();
            
            $allParticipants = array_unique(array_merge($feedbackAuthors, $consentAuthors));
            $participated = count(array_intersect($eligible, $allParticipants));
        }

        $participationStats = [
            'eligible'     => count($eligible),
            'participated' => $participated,
            'pending'      => max(0, count($eligible) - $participated)
        ];

        $decision->setAttribute('participation_stats', $participationStats);

        $myConsent = null;
        if ($v) {
            $myConsent = Consent::where('decision_version_id', $v->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        $hasFeedbackInPhase = false;
        $hasConsentInPhase = false;

        if ($v && !empty($phaseFeedbackTypes)) {
            $hasFeedbackInPhase = Feedback::where('decision_version_id', $v->id)
                ->where('author_id', auth()->id())
                ->whereIn('type', $phaseFeedbackTypes)
                ->exists();

            $hasConsentInPhase = $myConsent && in_array($myConsent->signal->value, $phaseConsentSignals);
        }

        $phaseParticipationMap = [
            'clarification' => [],
            'reaction' => [],
            'objection' => [],
        ];

        if ($v) {
            $versionFeedbacks = $v->feedbacks()->get(['author_id', 'type']);
            $versionConsents = $v->consents()->get(['user_id', 'signal']);

            foreach ($versionFeedbacks as $feedback) {
                if ($feedback->type->value === FeedbackType::CLARIFICATION->value) {
                    $phaseParticipationMap['clarification'][$feedback->author_id] = true;
                }
                if ($feedback->type->value === FeedbackType::REACTION->value) {
                    $phaseParticipationMap['reaction'][$feedback->author_id] = true;
                }
                if (in_array($feedback->type->value, [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value], true)) {
                    $phaseParticipationMap['objection'][$feedback->author_id] = true;
                }
            }

            foreach ($versionConsents as $consent) {
                if ($consent->signal->value === ConsentSignal::NO_QUESTIONS->value) {
                    $phaseParticipationMap['clarification'][$consent->user_id] = true;
                }
                if ($consent->signal->value === ConsentSignal::NO_REACTION->value) {
                    $phaseParticipationMap['reaction'][$consent->user_id] = true;
                }
                if (in_array($consent->signal->value, [ConsentSignal::NO_OBJECTION->value, ConsentSignal::ABSTENTION->value], true)) {
                    $phaseParticipationMap['objection'][$consent->user_id] = true;
                }
            }
        }

        return response()->json([
            'decision' => $decision->load([
                'currentVersion.attachments', 
                'currentVersion.feedbacks', 
                'currentVersion.consents', 
                'participants.user', 
                'circle.members.user',
                'category', 
                'decisionModel'
            ]),
            'participation_stats' => $participationStats,
            'has_participated' => $hasFeedbackInPhase || $hasConsentInPhase,
            'phase_participation_map' => $phaseParticipationMap,
            'my_consent' => $myConsent ? [
                'signal'              => $myConsent->signal,
                'has_participated'    => $hasFeedbackInPhase || $hasConsentInPhase,
            ] : [
                'has_participated'    => $hasFeedbackInPhase || $hasConsentInPhase,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('update', $decision);

        if ($decision->status->value !== DecisionStatus::DRAFT->value) {
            abort(400, "Seule une décision en brouillon peut être modifiée ainsi.");
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'animator_id' => 'nullable|uuid',
            'excluded_members' => 'nullable|array',
            'excluded_members.*' => 'uuid',
            'category_id' => 'nullable|exists:categories,id',
            'model_id' => 'nullable|exists:decision_models,id',
        ]);

        $decision->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? null,
            'model_id' => $validated['model_id'] ?? null,
        ]);

        $version = $decision->currentVersion;
        if ($version) {
            $version->update(['content' => $validated['content']]);
        }

        // Sync Animator
        $decision->participants()->where('role', DecisionParticipantRole::ANIMATOR->value)->delete();
        if (!empty($validated['animator_id'])) {
            $decision->participants()->create([
                'user_id' => $validated['animator_id'],
                'role' => DecisionParticipantRole::ANIMATOR->value,
            ]);
        }

        // Sync Excluded
        $decision->participants()->where('role', DecisionParticipantRole::EXCLUDED->value)->delete();
        if (!empty($validated['excluded_members'])) {
            foreach ($validated['excluded_members'] as $userId) {
                // Ensure the excluded user is not author or animator (simplified)
                $decision->participants()->firstOrCreate([
                    'user_id' => $userId,
                    'role' => DecisionParticipantRole::EXCLUDED->value,
                ]);
            }
        }

        return response()->json([
            'message' => 'Décision mise à jour.',
            'decision' => $decision->fresh(['currentVersion', 'participants.user'])
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('delete', $decision);

        if ($decision->status->value !== DecisionStatus::DRAFT->value) {
            abort(400, "Seul un brouillon peut être supprimé.");
        }

        $decision->delete();

        return response()->json([
            'message' => 'Brouillon supprimé avec succès.',
        ]);
    }

    public function updateAnimator(\Illuminate\Http\Request $request, string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $user = $request->user();

        // Seul l'auteur, l'animateur actuel, ou l'admin peut changer l'animateur
        $isAuthorOrAnimator = $decision->participants()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                \App\Enums\DecisionParticipantRole::AUTHOR->value,
                \App\Enums\DecisionParticipantRole::ANIMATOR->value,
            ])->exists();

        if (!$isAuthorOrAnimator && $user->role->value !== \App\Enums\UserRole::ADMIN->value) {
            abort(403, "Vous n'avez pas la permission de modifier l'animateur.");
        }

        if ($decision->status->value === \App\Enums\DecisionStatus::ADOPTED->value) {
            abort(400, "Impossible de modifier l'animateur d'une décision adoptée.");
        }

        $validated = $request->validate([
            'animator_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        // Supprimer l'animateur actuel
        $decision->participants()
            ->where('role', \App\Enums\DecisionParticipantRole::ANIMATOR->value)
            ->delete();

        // Assigner le nouvel animateur si fourni
        if (!empty($validated['animator_id'])) {
            $decision->participants()->create([
                'user_id' => $validated['animator_id'],
                'role'    => \App\Enums\DecisionParticipantRole::ANIMATOR->value,
            ]);
        }

        return response()->json([
            'message'  => 'Animateur mis à jour.',
            'decision' => $decision->fresh(['participants.user']),
        ]);
    }
}
