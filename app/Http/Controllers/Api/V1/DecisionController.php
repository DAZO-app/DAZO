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

use App\Traits\HasUserActionStatus;

class DecisionController extends Controller
{
    use HasUserActionStatus;

    public function __construct(private DecisionService $decisionService)
    {
    }

    public function mine(): JsonResponse
    {
        $decisions = Decision::whereHas('circle.members', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['circle', 'category', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user'])
            ->latest()
            ->get();

        $this->attachParticipationStats($decisions);
        $this->attachUserActionStatus($decisions, auth()->id());

        return response()->json(['decisions' => $decisions]);
    }

    public function index(string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        $this->authorize('view', $circle);

        $decisions = Decision::where('circle_id', $circle->id)
            ->with(['circle', 'category', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user'])
            ->get();

        $this->attachParticipationStats($decisions);
        $this->attachUserActionStatus($decisions, auth()->id());

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

        $decision->load([
            'currentVersion.attachments', 
            'currentVersion.feedbacks', 
            'currentVersion.consents', 
            'participants.user', 
            'circle.members.user',
            'category', 
            'decisionModel'
        ]);

        $participationStats = [
            'eligible'     => 0,
            'participated' => 0,
            'pending'      => 0
        ];

        $v = $decision->currentVersion;

        $phaseFeedbackTypes = [];
        $phaseConsentSignals = [];

        if ($v) {
            $participationStats = $this->decisionService->getParticipationStats($decision, $v);
            
            // Re-calcul des types de phase pour la suite du controller
            $status = $decision->status->value;
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
        }

        $decision->setAttribute('participation_stats', $participationStats);
        $decision->setAttribute('user_status', $this->calculateUserActionStatus($decision, auth()->id()));

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
            'decision' => $decision,
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

        if ($decision->status->value !== DecisionStatus::DRAFT->value && $decision->status->value !== DecisionStatus::REVISION->value) {
            abort(400, "Cette décision ne peut pas être modifiée dans son état actuel.");
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'animator_id' => 'nullable|uuid',
            'excluded_members' => 'nullable|array',
            'excluded_members.*' => 'uuid',
            'category_id' => 'nullable|exists:categories,id',
            'model_id' => 'nullable|exists:decision_models,id',
            'revision_attachment_ids' => 'nullable|array',
            'revision_attachment_ids.*' => 'exists:attachments,id',
        ]);

        $decision->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? null,
            'model_id' => $validated['model_id'] ?? null,
        ]);

        if ($decision->status->value === DecisionStatus::DRAFT->value) {
            $version = $decision->currentVersion;
            if ($version) {
                $version->update(['content' => $validated['content']]);
            }
        } else {
            // REVISION status: save to draft fields
            $decision->update([
                'revision_content' => $validated['content'],
                'revision_attachment_ids' => $validated['revision_attachment_ids'] ?? null,
            ]);
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

    private function attachParticipationStats($decisions)
    {
        if ($decisions->isEmpty()) return;

        $decisions->loadMissing(['circle.members', 'currentVersion.feedbacks', 'currentVersion.consents']);
        
        foreach ($decisions as $decision) {
            $circle = $decision->circle;
            if (!$circle) continue;
            
            $totalMembers = $circle->members->where('role', '!=', CircleMemberRole::OBSERVER->value)->pluck('user_id')->toArray();
            $excludedOrManaging = $decision->participants
                ->whereIn('role', [
                    DecisionParticipantRole::EXCLUDED->value,
                    DecisionParticipantRole::AUTHOR->value,
                    DecisionParticipantRole::ANIMATOR->value
                ])->pluck('user_id')->toArray();
            
            $eligible = array_diff($totalMembers, $excludedOrManaging);
            $v = $decision->currentVersion;
            $participated = 0;
            $status = $decision->status->value;

            if ($v && in_array($status, [DecisionStatus::CLARIFICATION->value, DecisionStatus::REACTION->value, DecisionStatus::OBJECTION->value], true)) {
                $phaseFeedbackTypes = [];
                $phaseConsentSignals = [];

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

                $feedbackAuthors = [];
                foreach ($v->feedbacks as $fb) {
                    $typeVal = is_object($fb->type) ? $fb->type->value : $fb->type;
                    if (in_array($typeVal, $phaseFeedbackTypes)) {
                        $feedbackAuthors[] = $fb->author_id;
                    }
                }

                $consentAuthors = [];
                foreach ($v->consents as $cs) {
                    $signalVal = is_object($cs->signal) ? $cs->signal->value : $cs->signal;
                    if (in_array($signalVal, $phaseConsentSignals)) {
                        $consentAuthors[] = $cs->user_id;
                    }
                }
                
                $allParticipants = array_unique(array_merge($feedbackAuthors, $consentAuthors));
                $participated = count(array_intersect($eligible, $allParticipants));
            }

            $decision->setAttribute('participation_stats', [
                'eligible'     => count($eligible),
                'participated' => $participated,
            ]);
        }
    }
}
