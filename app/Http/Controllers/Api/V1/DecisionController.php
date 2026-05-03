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
use App\Http\Resources\V1\DecisionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DecisionController extends Controller
{

    public function __construct(
        private DecisionService $decisionService,
        private \App\Services\ConfigService $configService,
        private \App\Services\DecisionParticipationService $participationService
    ) {
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $userId = auth()->id();
        $perPage = $request->integer('per_page', 20);
        $perPage = min(max($perPage, 5), 100);

        $query = Decision::where(function ($query) use ($userId) {
            $query->whereHas('circle.members', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orWhereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        })
        ->with(['circle', 'categories', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user']);

        $this->applyFilters($query, $request);

        $decisions = $query->paginate($perPage);

        $this->attachParticipationStats($decisions->getCollection());
        $this->participationService->attachUserActionStatus($decisions->getCollection(), auth()->id());
        $this->attachUserSettings($decisions->getCollection(), auth()->id());

        return DecisionResource::collection($decisions);
    }

    public function index(Request $request, string $circleId): AnonymousResourceCollection
    {
        $circle = Circle::findOrFail($circleId);
        $this->authorize('view', $circle);

        $perPage = $request->integer('per_page', 20);
        $perPage = min(max($perPage, 5), 100);

        $query = Decision::where('circle_id', $circle->id)
            ->with(['circle', 'categories', 'currentVersion.attachments', 'author.user', 'decisionModel', 'participants.user']);

        $this->applyFilters($query, $request);

        $decisions = $query->paginate($perPage);

        $this->attachParticipationStats($decisions->getCollection());
        $this->participationService->attachUserActionStatus($decisions->getCollection(), auth()->id());
        $this->attachUserSettings($decisions->getCollection(), auth()->id());

        return DecisionResource::collection($decisions);
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

    public function show(string $id): DecisionResource
    {
        $decision = Decision::findOrFail($id);
        $this->authorize('view', $decision);

        $decision->load([
            'currentVersion.attachments',
            'currentVersion.feedbacks',
            'currentVersion.consents.user',
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
            $participationStats     = $this->participationService->getParticipationStats($decision, $v);
            $phaseParticipationMap  = $this->participationService->getPhaseParticipationMap($decision, $v);

            $myConsent = Consent::where('decision_version_id', $v->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($phaseConfig) {
                $participatedIds = $this->participationService->getParticipatedUserIds($v, $phaseConfig);
                $hasParticipated = in_array(auth()->id(), $participatedIds);
                $hasFeedbackInPhase = $hasParticipated; // Simplified for additional block
                $hasConsentInPhase = $hasParticipated;  // Simplified for additional block
            }
        }

        $decision->setAttribute('participation_stats', $participationStats);
        $decision->setAttribute('user_status', $this->participationService->calculateUserActionStatus($decision, auth()->id()));

        if ($decision->status->value === DecisionStatus::REVISION->value && !empty($decision->revision_attachment_ids)) {
            $decision->setAttribute('revision_attachments', \App\Models\Attachment::whereIn('id', $decision->revision_attachment_ids)->get());
        }

        $mySettings = \App\Models\DecisionUserSetting::where('user_id', auth()->id())
            ->where('decision_id', $decision->id)
            ->first();

        // Note: We return the resource but we can still append the extra data for the specific view
        // In a real API, we might want to include these in the resource itself or as "meta"
        return (new DecisionResource($decision))->additional([
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

        foreach ($decisions as $decision) {
            $v = $decision->currentVersion;
            if (!$v) continue;

            $decision->setAttribute('participation_stats', $this->participationService->getParticipationStats($decision, $v));
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
            'pending_users' => $this->participationService->getPendingUsers($decision),
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

        $pendingUsers = $this->participationService->getPendingUsers($decision);

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

    /**
     * Apply query filters for private decisions.
     */
    private function applyFilters($query, Request $request): void
    {
        // Search
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhereHas('participants.user', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', $searchTerm);
                    });
            });
        }

        // State (special handling)
        if ($request->filled('state') && $request->state !== 'all') {
            $s = $request->state;
            if ($s === 'active') {
                $query->whereIn('status', ['clarification', 'reaction', 'objection']);
            } elseif ($s === 'adopted') {
                $query->whereIn('status', ['adopted', 'adopted_override']);
            } elseif ($s === 'abandoned') {
                $query->whereIn('status', ['abandoned', 'deserted', 'lapsed']);
            } else {
                $query->where('status', $s);
            }
        }

        // Circle
        if ($request->filled('circle') && $request->circle !== 'all') {
            $query->where('circle_id', $request->circle);
        }

        // Category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Author
        if ($request->filled('author') && $request->author !== 'all') {
            $query->whereHas('participants', function ($q) use ($request) {
                $q->where('role', 'author')->where('user_id', $request->author);
            });
        }

        // My Role filter
        if ($request->filled('my_role') && $request->my_role !== 'all') {
            $role = $request->my_role;
            $userId = auth()->id();
            $query->whereHas('participants', function ($q) use ($role, $userId) {
                $q->where('user_id', $userId)->where('role', $role);
            });
        }

        // Favorites only
        if ($request->boolean('favorites_only')) {
            $query->whereHas('userSettings', function ($q) {
                $q->where('user_id', auth()->id())->where('is_favorite', true);
            });
        }

        // Sorting
        $sort = $request->query('sort', 'created_desc');
        match ($sort) {
            'created_asc'  => $query->orderBy('created_at', 'asc'),
            'updated_desc' => $query->orderBy('updated_at', 'desc'),
            'updated_asc'  => $query->orderBy('updated_at', 'asc'),
            'alpha_asc'    => $query->orderBy('title', 'asc'),
            'alpha_desc'   => $query->orderBy('title', 'desc'),
            default        => $query->orderBy('created_at', 'desc'),
        };
    }
}
