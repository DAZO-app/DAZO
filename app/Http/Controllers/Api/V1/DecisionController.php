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

    public function __construct(
        private DecisionService $decisionService,
        private \App\Services\ConfigService $configService
    ) {
    }

    public function mine(): JsonResponse
    {
        $userId = auth()->id();
        $decisions = Decision::where(function ($query) use ($userId) {
            $query->whereHas('circle.members', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })
            ->with(['circle', 'categories', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user'])
            ->latest()
            ->get();

        $this->attachParticipationStats($decisions);
        $this->attachUserActionStatus($decisions, auth()->id());
        $this->attachUserSettings($decisions, auth()->id());

        return response()->json(['decisions' => $decisions]);
    }

    public function index(string $circleId): JsonResponse
    {
        $circle = Circle::findOrFail($circleId);
        $this->authorize('view', $circle);

        $decisions = Decision::where('circle_id', $circle->id)
            ->with(['circle', 'categories', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user'])
            ->get();

        $this->attachParticipationStats($decisions);
        $this->attachUserActionStatus($decisions, auth()->id());
        $this->attachUserSettings($decisions, auth()->id());

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
            'categories',
            'decisionModel',
        ]);

        $v                 = $decision->currentVersion;
        $phaseConfig       = $decision->status->getPhaseConfig();
        $participationStats = ['eligible' => 0, 'participated' => 0, 'pending' => 0];
        $phaseParticipationMap = ['clarification' => [], 'reaction' => [], 'objection' => []];
        $myConsent = null;
        $hasFeedbackInPhase = false;
        $hasConsentInPhase  = false;

        if ($v) {
            $participationStats     = $this->decisionService->getParticipationStats($decision, $v);
            $phaseParticipationMap  = $this->decisionService->getPhaseParticipationMap($decision, $v);

            $myConsent = Consent::where('decision_version_id', $v->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($phaseConfig) {
                $hasFeedbackInPhase = Feedback::where('decision_version_id', $v->id)
                    ->where('author_id', auth()->id())
                    ->whereIn('type', $phaseConfig['feedback_types'])
                    ->exists();

                $hasConsentInPhase = $myConsent
                    && in_array(
                        is_object($myConsent->signal) ? $myConsent->signal->value : $myConsent->signal,
                        $phaseConfig['consent_signals']
                    );
            }
        }

        $decision->setAttribute('participation_stats', $participationStats);
        $decision->setAttribute('user_status', $this->calculateUserActionStatus($decision, auth()->id()));

        if ($decision->status->value === DecisionStatus::REVISION->value && !empty($decision->revision_attachment_ids)) {
            $decision->setAttribute('revision_attachments', \App\Models\Attachment::whereIn('id', $decision->revision_attachment_ids)->get());
        }

        $mySettings = \App\Models\DecisionUserSetting::where('user_id', auth()->id())
            ->where('decision_id', $decision->id)
            ->first();

        return response()->json([
            'decision'               => $decision,
            'participation_stats'    => $participationStats,
            'has_participated'       => $hasFeedbackInPhase || $hasConsentInPhase,
            'phase_participation_map' => $phaseParticipationMap,
            'my_settings'            => $mySettings,
            'my_consent' => $myConsent ? [
                'signal'           => $myConsent->signal,
                'has_participated' => $hasFeedbackInPhase || $hasConsentInPhase,
            ] : [
                'has_participated' => $hasFeedbackInPhase || $hasConsentInPhase,
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
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'model_id' => 'nullable|exists:decision_models,id',
            'revision_attachment_ids' => 'nullable|array',
            'revision_attachment_ids.*' => 'exists:attachments,id',
        ]);

        $decision->update([
            'title' => $validated['title'],
            'model_id' => $validated['model_id'] ?? null,
        ]);

        if (isset($validated['category_ids'])) {
            $decision->categories()->sync($validated['category_ids']);
        }

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

    private function attachParticipationStats($decisions): void
    {
        if ($decisions->isEmpty()) return;

        $decisions->loadMissing([
            'circle.members',
            'currentVersion.feedbacks',
            'currentVersion.consents',
            'categories',
        ]);

        foreach ($decisions as $decision) {
            $circle = $decision->circle;
            if (!$circle) continue;

            $phaseConfig   = $decision->status->getPhaseConfig();
            $totalMembers  = $circle->members
                ->where('role', '!=', CircleMemberRole::OBSERVER->value)
                ->pluck('user_id')->toArray();

            $excludedOrManaging = $decision->participants
                ->whereIn('role', [
                    DecisionParticipantRole::EXCLUDED->value,
                    DecisionParticipantRole::AUTHOR->value,
                    DecisionParticipantRole::ANIMATOR->value,
                ])->pluck('user_id')->toArray();

            $eligible    = array_diff($totalMembers, $excludedOrManaging);
            $v           = $decision->currentVersion;
            $participated = 0;

            if ($v && $phaseConfig) {
                $feedbackAuthors = $v->feedbacks
                    ->filter(fn($fb) => in_array(
                        is_object($fb->type) ? $fb->type->value : $fb->type,
                        $phaseConfig['feedback_types']
                    ))->pluck('author_id')->toArray();

                $consentAuthors = $v->consents
                    ->filter(fn($cs) => in_array(
                        is_object($cs->signal) ? $cs->signal->value : $cs->signal,
                        $phaseConfig['consent_signals']
                    ))->pluck('user_id')->toArray();

                $allParticipants = array_unique(array_merge($feedbackAuthors, $consentAuthors));
                $participated    = count(array_intersect($eligible, $allParticipants));
            }

            $decision->setAttribute('participation_stats', [
                'eligible'    => count($eligible),
                'participated' => $participated,
            ]);
        }
    }

    private function attachUserSettings($decisions, $userId): void
    {
        $settings = \App\Models\DecisionUserSetting::where('user_id', $userId)
            ->whereIn('decision_id', $decisions->pluck('id'))
            ->get()
            ->keyBy('decision_id');

        foreach ($decisions as $decision) {
            $decision->setAttribute('my_settings', $settings->get($decision->id));
        }
    }

    public function getPendingParticipants(string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('view', $decision);

        return response()->json([
            'pending_users' => $this->decisionService->getPendingUsers($decision),
        ]);
    }

    public function remind(Request $request, string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);

        $isAuthorOrAnimator = $decision->participants()
            ->where('user_id', auth()->id())
            ->whereIn('role', [
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->exists();

        if (!$isAuthorOrAnimator && auth()->user()->role->value !== \App\Enums\UserRole::ADMIN->value) {
            abort(403, "Seul le porteur ou l'animateur peut envoyer des relances.");
        }

        $this->configService->applyMailConfig();

        $pendingUsers = $this->decisionService->getPendingUsers($decision);

        if ($pendingUsers->isEmpty()) {
            return response()->json(['message' => "Aucun participant en attente à relancer."], 200);
        }

        foreach ($pendingUsers as $user) {
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(
                new \App\Mail\DecisionReminderMail($decision, $user)
            );
        }

        return response()->json([
            'message' => count($pendingUsers) . " mail(s) de relance envoyés avec succès.",
        ]);
    }
}
