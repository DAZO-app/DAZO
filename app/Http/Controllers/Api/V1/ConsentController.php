<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\CreateConsentRequest;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\Feedback;
use App\Models\FeedbackJoin;
use App\Enums\CircleMemberRole;
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
        if ($request->has('acting_as_user_id')) {
            $user = \App\Models\User::findOrFail($request->acting_as_user_id);
        }
        $decision = Decision::findOrFail($decisionId);
        abort_unless($decision->versions()->whereKey($versionId)->exists(), 404);

        // Règle exclusive de non-cumul limitée à la phase
        $status = $decision->status->value;
        $phaseFeedbackTypes = [];
        if ($status === \App\Enums\DecisionStatus::CLARIFICATION->value) {
            $phaseFeedbackTypes = [\App\Enums\FeedbackType::CLARIFICATION->value];
        } elseif ($status === \App\Enums\DecisionStatus::REACTION->value) {
            $phaseFeedbackTypes = [\App\Enums\FeedbackType::REACTION->value];
        } elseif ($status === \App\Enums\DecisionStatus::OBJECTION->value) {
            // Pour l'objection, on ne bloque le consentement que si une objection REELLE existe
            $phaseFeedbackTypes = [\App\Enums\FeedbackType::OBJECTION->value];
        }

        // On ne bloque le consentement que pour la phase CLARIFICATION (si question en suspens)
        // ou pour l'OBJECTION (si objection en suspens).
        // On autorise le cumul pour REACTION et SUGGESTION.
        if (in_array($status, [\App\Enums\DecisionStatus::CLARIFICATION->value, \App\Enums\DecisionStatus::OBJECTION->value])) {
            $hasFeedbackInPhase = Feedback::where('decision_version_id', $versionId)
                ->where('author_id', $user->id)
                ->whereIn('type', $phaseFeedbackTypes)
                ->exists();
            
            if ($hasFeedbackInPhase) {
                throw ValidationException::withMessages(['general' => "Vous avez déjà soumis un retour pour cette phase."]);
            }

            $hasJoinedInPhase = FeedbackJoin::whereHas('feedback', function($q) use ($versionId, $phaseFeedbackTypes) {
                $q->where('decision_version_id', $versionId)->whereIn('type', $phaseFeedbackTypes);
            })->where('user_id', $user->id)->exists();
            
            if ($hasJoinedInPhase) {
                throw ValidationException::withMessages(['general' => "Vous soutenez déjà un retour pour cette phase."]);
            }
        }

        $consent = \App\Models\Consent::updateOrCreate(
            ['decision_version_id' => $versionId, 'user_id' => $user->id, 'phase' => $status],
            [
                'signal' => $request->type,
                'acting_as_user_id' => $request->acting_as_user_id,
            ]
        );

        if ($request->boolean('notify', true)) {
            // event(new \App\Events\ConsentSubmitted($consent));
        }

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

    public function destroy(string $consentId): JsonResponse
    {
        $consent = Consent::findOrFail($consentId);
        
        $user = request()->user();
        $member = $consent->decisionVersion->decision->circle->members()->where('user_id', $user->id)->first();
        $isAuthorized = ($member && $member->role->value !== \App\Enums\CircleMemberRole::OBSERVER->value) || $user->is_global_animator;

        abort_unless(
            $isAuthorized, 
            403, 
            'Seul un membre du cercle (non observateur) peut annuler cette action.'
        );

        $consent->delete();

        return response()->json(['message' => 'Consentement annulé.'], 200);
    }
}
