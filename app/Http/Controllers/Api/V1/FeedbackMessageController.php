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

        $message = $feedback->messages()->create([
            'author_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Message publié sur le feedback.',
            'feedback_message' => $message->load('author'),
        ], 201);
    }
}
