<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\CreateConsentRequest;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\Feedback;
use App\Models\FeedbackJoin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConsentController extends Controller
{
    public function index(Request $request, string $decisionId, string $versionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        if ($request->user()->cannot('view', $decision)) abort(403);
        abort_unless($decision->versions()->whereKey($versionId)->exists(), 404);

        $consents = Consent::where('decision_version_id', $versionId)
            ->with('user')
            ->get();

        return response()->json(['consents' => $consents]);
    }

    public function store(CreateConsentRequest $request, string $decisionId, string $versionId): JsonResponse
    {
        $user = $request->user();
        $decision = Decision::findOrFail($decisionId);
        abort_unless($decision->versions()->whereKey($versionId)->exists(), 404);

        // Règle exclusive de non-cumul (ni feedback ni soutiens)
        $hasFeedback = Feedback::where('decision_version_id', $versionId)->where('author_id', $user->id)->exists();
        if ($hasFeedback) throw ValidationException::withMessages(['general' => "Vous avez soumis un feedback sur cette version."]);

        $hasJoined = FeedbackJoin::whereHas('feedback', function($q) use ($versionId) {
            $q->where('decision_version_id', $versionId);
        })->where('user_id', $user->id)->exists();
        if ($hasJoined) throw ValidationException::withMessages(['general' => "Vous soutenez un feedback sur cette version."]);

        $consent = Consent::updateOrCreate(
            ['decision_version_id' => $versionId, 'user_id' => $user->id],
            ['signal' => $request->type]
        );

        // Assure que l'utilisateur est bien listé comme participant
        $exists = $decision->participants()->where('user_id', $user->id)->exists();
        if (!$exists) {
            $decision->participants()->create([
                'user_id' => $user->id,
                'role' => \App\Enums\DecisionParticipantRole::PARTICIPANT->value,
            ]);
        }

        return response()->json([
            'message' => 'Consentement enregistré.',
            'consent' => $consent,
        ], 201);
    }
}
