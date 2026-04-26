<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\CreateFeedbackRequest;
use App\Http\Requests\Feedback\UpdateFeedbackStatusRequest;
use App\Models\Decision;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(private FeedbackService $feedbackService)
    {
    }

    public function index(Request $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        if ($request->user()->cannot('view', $decision)) abort(403);

        $versionId = $request->query('version_id', $decision->currentVersion->id);
        abort_unless($decision->versions()->whereKey($versionId)->exists(), 404);

        $feedbacks = Feedback::where('decision_version_id', $versionId)
            ->with(['author', 'joins', 'messages.author'])
            ->get();

        $consents = \App\Models\Consent::where('decision_version_id', $versionId)
            ->with('user')
            ->get()
            ->groupBy('signal')
            ->map(function ($group) {
                return [
                    'signal' => $group->first()->signal,
                    'users' => $group->map(fn ($c) => $c->user?->name)->filter()->values()
                ];
            })
            ->values();

        return response()->json([
            'feedbacks' => $feedbacks,
            'consents' => $consents
        ]);
    }

    public function store(CreateFeedbackRequest $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        $user = $request->user();
        if ($request->has('acting_as_user_id')) {
            $user = \App\Models\User::findOrFail($request->acting_as_user_id);
        }
        
        $feedback = $this->feedbackService->submitFeedback($decision, $request->validated(), $user);

        return response()->json([
            'message' => 'Feedback soumis.',
            'feedback' => $feedback->load('author'),
        ], 201);
    }

    public function show(Request $request, string $decisionId, string $feedbackId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        if ($request->user()->cannot('view', $decision)) abort(403);

        $feedback = Feedback::where('id', $feedbackId)
            ->whereHas('version', fn ($query) => $query->where('decision_id', $decision->id))
            ->with(['author', 'joins.user', 'messages.author'])
            ->firstOrFail();

        return response()->json(['feedback' => $feedback]);
    }

    public function updateStatus(UpdateFeedbackStatusRequest $request, string $decisionId, string $feedbackId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        if ($request->user()->cannot('view', $decision)) abort(403);

        $feedback = Feedback::where('id', $feedbackId)
            ->whereHas('version', fn ($query) => $query->where('decision_id', $decision->id))
            ->firstOrFail();

        $feedback = $this->feedbackService->changeStatus($feedback, $request->status, $request->user());

        return response()->json([
            'message' => 'Statut du feedback mis à jour.',
            'feedback' => $feedback
        ]);
    }

    public function join(Request $request, string $feedbackId): JsonResponse
    {
        $feedback = Feedback::with('version.decision')->findOrFail($feedbackId);
        $decision = $feedback->version->decision;
        if ($request->user()->cannot('view', $decision)) abort(403);

        $join = $this->feedbackService->joinFeedback($decision, $feedback, $request->user());

        return response()->json([
            'message' => 'Vous soutenez ce feedback.',
            'join' => $join
        ], 201);
    }

    public function destroy(string $feedbackId): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedbackId);
        
        $decision = $feedback->version->decision;
        $userRole = $decision->participants()->where('user_id', request()->user()->id)->first()?->role;
        abort_unless($userRole === \App\Enums\DecisionParticipantRole::ANIMATOR, 403, 'Seul l\'animateur peut annuler cette action.');

        $feedback->delete();

        return response()->json(['message' => 'Feedback annulé.'], 200);
    }
}
