<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DecisionParticipantRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Thread\CreateThreadMessageRequest;
use App\Models\Decision;
use App\Models\ThreadMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThreadMessageController extends Controller
{
    public function index(Request $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        if ($request->user()->cannot('view', $decision)) {
            abort(403);
        }

        $query = $decision->threadMessages()->with('author');
        
        if ($request->has('tour')) {
            $query->where('tour', $request->query('tour'));
        }

        return response()->json([
            'messages' => $query->orderBy('created_at', 'asc')->get()
        ]);
    }

    public function store(CreateThreadMessageRequest $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        $user = $request->user();

        $isModeratorNote = false;
        if ($request->boolean('is_moderator_note')) {
            $isAnimator = $decision->participants()
                ->where('user_id', $user->id)
                ->where('role', DecisionParticipantRole::ANIMATOR->value)
                ->exists();
            if ($isAnimator || $user->is_global_animator) {
                $isModeratorNote = true;
            }
        }

        $message = ThreadMessage::create([
            'decision_id' => $decision->id,
            'author_id' => $user->id,
            'tour' => $decision->status->value, // Matches the exact phase implicitly (clarification or reaction)
            'content' => $request->content,
            'is_moderator_note' => $isModeratorNote,
        ]);

        return response()->json([
            'message' => 'Message publié.',
            'thread_message' => $message->load('author'),
        ], 201);
    }
}
