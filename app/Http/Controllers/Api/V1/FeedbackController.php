<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\CreateFeedbackRequest;
use App\Http\Requests\Feedback\UpdateFeedbackStatusRequest;
use App\Models\Decision;
use App\Models\Feedback;
use App\Services\FeedbackService;
use App\Enums\CircleMemberRole;
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
            ->with(['author', 'joins.user', 'messages.author'])
            ->get();

        $consents = \App\Models\Consent::where('decision_version_id', $versionId)
            ->with('user')
            ->get();

        return response()->json([
            'feedbacks' => $feedbacks,
            'consents' => $consents,
        ]);
    }

    public function store(CreateFeedbackRequest $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        $user = $request->user();
        if ($request->has('acting_as_user_id')) {
            $user = \App\Models\User::findOrFail($request->acting_as_user_id);
        }
        
        $feedback = $this->feedbackService->submitFeedback(
            $decision, 
            $request->validated(), 
            $user,
            $request->boolean('notify', true)
        );

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

        $feedback = $this->feedbackService->changeStatus(
            $feedback, 
            $request->status, 
            $request->user(),
            $request->boolean('notify', true)
        );

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

        $user = $request->user();
        $targetUser = $user;
        
        if ($request->has('user_id')) {
            // Check if requester has animator/author/admin OR circle member rights to act as secretary
            $isSecretary = $user->is_global_animator 
                || in_array(optional($user->role)->value, ['superadmin', 'admin'])
                || $decision->participants()
                    ->where('user_id', $user->id)
                    ->whereIn('role', ['animator', 'author'])
                    ->exists()
                || $decision->circle->members()
                    ->where('user_id', $user->id)
                    ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value)
                    ->exists();
            
            if ($isSecretary) {
                $targetId = $request->input('user_id');
                if ($targetId) {
                    $targetUser = \App\Models\User::find($targetId) ?? $user;
                }
            }
        }

        $join = $this->feedbackService->joinFeedback($decision, $feedback, $targetUser);

        return response()->json([
            'message' => 'Vous soutenez ce feedback.',
            'join' => $join
        ], 201);
    }

    public function destroy(string $feedbackId): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedbackId);
        
        $decision = $feedback->version->decision;
        $user = request()->user();
        $member = $decision->circle->members()->where('user_id', $user->id)->first();
        $isAuthorized = ($member && $member->role->value !== \App\Enums\CircleMemberRole::OBSERVER->value) || $user->is_global_animator;

        abort_unless(
            $isAuthorized, 
            403, 
            'Seul un membre du cercle (non observateur) peut annuler cette action.'
        );

        $feedback->delete();

        return response()->json(['message' => 'Feedback annulé.'], 200);
    }
}
