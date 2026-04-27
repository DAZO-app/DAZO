<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\CreateFeedbackMessageRequest;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class FeedbackMessageController extends Controller
{
    public function index(string $feedbackId): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedbackId);
        
        $decision = $feedback->decisionVersion->decision;
        if (request()->user()->cannot('view', $decision)) abort(403);

        $messages = $feedback->messages()->with('author')->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(CreateFeedbackMessageRequest $request, string $feedbackId): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedbackId);

        $authorId = $request->user()->id;
        if ($request->has('acting_as_user_id')) {
            $user = $request->user();
            $decision = $feedback->version->decision;
            $isSecretary = $user->is_global_animator 
                || in_array(optional($user->role)->value, ['superadmin', 'admin'])
                || $decision->participants()
                    ->where('user_id', $user->id)
                    ->whereIn('role', [\App\Enums\DecisionParticipantRole::ANIMATOR->value, \App\Enums\DecisionParticipantRole::AUTHOR->value])
                    ->exists()
                || $decision->circle->members()
                    ->where('user_id', $user->id)
                    ->where('role', '!=', \App\Enums\CircleMemberRole::OBSERVER->value)
                    ->exists();

            if ($isSecretary) {
                $authorId = $request->acting_as_user_id;
            }
        }

        $message = $feedback->messages()->create([
            'author_id' => $authorId,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Message publié sur le feedback.',
            'feedback_message' => $message->load('author'),
        ], 201);
    }

    public function destroy(string $messageId): JsonResponse
    {
        $message = \App\Models\FeedbackMessage::findOrFail($messageId);
        
        $decision = $message->feedback->version->decision;
        $userRole = $decision->participants()->where('user_id', request()->user()->id)->first()?->role;
        abort_unless($userRole === \App\Enums\DecisionParticipantRole::ANIMATOR, 403, 'Seul l\'animateur peut annuler cette action.');

        $message->delete();

        return response()->json(['message' => 'Message annulé.'], 200);
    }
}
