<?php

namespace App\Services;

use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\FeedbackJoin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeedbackService
{
    /**
     * Submit a new feedback. Validates mutual exclusivity.
     */
    public function submitFeedback(Decision $decision, array $data, User $user, bool $notify = true): Feedback
    {
        return DB::transaction(function () use ($decision, $data, $user, $notify) {
            $versionId = $decision->currentVersion->id;

            $this->ensureExclusiveAction($versionId, $user->id, $data['type']);

            // Clear any simple consent (RAS/Abstention) for this phase
            // We also clear NULL phase records for safety with old data
            $phase = $this->mapTypeToPhase($data['type']);
            $phaseEnum = DecisionStatus::tryFrom($phase);
            
            Consent::where('decision_version_id', $versionId)
                ->where('user_id', $user->id)
                ->where(function($q) use ($phase, $phaseEnum) {
                    $q->where('phase', $phase)
                      ->orWhereNull('phase');
                    if ($phaseEnum) {
                        $q->orWhere('phase', $phaseEnum);
                    }
                })
                ->delete();

            $feedback = Feedback::create([
                'decision_version_id' => $versionId,
                'author_id' => $user->id,
                'type' => $data['type'],
                'status' => FeedbackStatus::SUBMITTED->value,
                'content' => $data['content'],
            ]);

            // Add participant record automatically if this is first action
            $this->ensureParticipant($decision, $user);

            if ($notify) {
                event(new \App\Events\FeedbackSubmitted($feedback));
            }

            return $feedback;
        });
    }

    public function joinFeedback(Decision $decision, Feedback $feedback, User $user): FeedbackJoin
    {
        return DB::transaction(function () use ($decision, $feedback, $user) {
            $this->ensureExclusiveAction($feedback->decision_version_id, $user->id, $feedback->type->value);

            // Clear any simple consent (RAS/Abstention) for this phase
            $phase = $this->mapTypeToPhase($feedback->type->value);
            $phaseEnum = DecisionStatus::tryFrom($phase);

            Consent::where('decision_version_id', $feedback->decision_version_id)
                ->where('user_id', $user->id)
                ->where(function($q) use ($phase, $phaseEnum) {
                    $q->where('phase', $phase)
                      ->orWhereNull('phase');
                    if ($phaseEnum) {
                        $q->orWhere('phase', $phaseEnum);
                    }
                })
                ->delete();

            $join = FeedbackJoin::create([
                'feedback_id' => $feedback->id,
                'user_id' => $user->id,
            ]);

            $this->ensureParticipant($decision, $user);

            return $join;
        });
    }

    public function changeStatus(Feedback $feedback, string $newStatus, User $actor, bool $notify = true): Feedback
    {
        $feedback->loadMissing('version.decision.participants');
        $decision = $feedback->version->decision;

        $canManageFeedback = $feedback->author_id === $actor->id
            || $decision->participants->contains(fn ($participant) => $participant->user_id === $actor->id
                && in_array($participant->role->value, [
                    \App\Enums\DecisionParticipantRole::AUTHOR->value,
                    \App\Enums\DecisionParticipantRole::ANIMATOR->value,
                ], true))
            || $actor->is_global_animator;

        if (! $canManageFeedback) {
            throw ValidationException::withMessages([
                'status' => ["Vous n'avez pas les droits pour modifier ce feedback."],
            ]);
        }

        $feedback->status = $newStatus;
        $feedback->save();

        if (in_array($newStatus, [FeedbackStatus::TREATED->value, FeedbackStatus::REJECTED->value])) {
            if ($notify) {
                event(new \App\Events\FeedbackResolved($feedback));
            }
        }

        // Auto-check for Adoption (Bloc 8)
        $this->checkAndAdoptIfNoBlockingObjections($decision, $notify);

        return $feedback;
    }

    /**
     * Automatically transitions decision to ADOPTED if 0 blocking objections
     */
    public function checkAndAdoptIfNoBlockingObjections(Decision $decision, bool $notify = true): void
    {
        if ($decision->status->value !== DecisionStatus::OBJECTION->value) return;

        $hasBlocking = ! $this->hasNoBlockingObjections($decision->currentVersion);

        if (!$hasBlocking) {
            // Adopt logic
            $decision->status = DecisionStatus::ADOPTED->value;
            $decision->save();
            if ($notify) {
                event(new \App\Events\DecisionAdopted($decision));
            }
        }
    }

    public function hasNoBlockingObjections(DecisionVersion $version): bool
    {
        return ! Feedback::where('decision_version_id', $version->id)
            ->where('type', \App\Enums\FeedbackType::OBJECTION->value)
            ->whereIn('status', [
                FeedbackStatus::SUBMITTED->value,
                FeedbackStatus::CLARIFICATION_REQUESTED->value,
                FeedbackStatus::IN_TREATMENT->value,
            ])->exists();
    }

    private function mapTypeToPhase(string $type): string
    {
        return match($type) {
            'clarification' => 'clarification',
            'reaction' => 'reaction',
            'objection', 'suggestion' => 'objection',
            default => $type
        };
    }

    private function ensureExclusiveAction(string $versionId, string $userId, ?string $type = null): void
    {
        $phaseTypes = match($type) {
            'clarification' => [\App\Enums\FeedbackType::CLARIFICATION->value],
            'reaction'      => [\App\Enums\FeedbackType::REACTION->value],
            'objection', 'suggestion' => [
                \App\Enums\FeedbackType::OBJECTION->value,
                \App\Enums\FeedbackType::SUGGESTION->value
            ],
            default => []
        };

        if (!empty($phaseTypes)) {
            $hasFeedback = Feedback::where('decision_version_id', $versionId)
                ->where('author_id', $userId)
                ->whereIn('type', $phaseTypes)
                ->exists();
            if ($hasFeedback) throw ValidationException::withMessages(['general' => "Vous avez déjà soumis un retour pour cette phase."]);

            $hasJoined = FeedbackJoin::whereHas('feedback', function($q) use ($versionId, $phaseTypes) {
                $q->where('decision_version_id', $versionId)->whereIn('type', $phaseTypes);
            })->where('user_id', $userId)->exists();
            if ($hasJoined) throw ValidationException::withMessages(['general' => "Vous soutenez déjà un retour pour cette phase."]);
        }
    }

    private function ensureParticipant(Decision $decision, User $user): void
    {
        $exists = $decision->participants()->where('user_id', $user->id)->exists();
        if (!$exists) {
            $decision->participants()->create([
                'user_id' => $user->id,
                'role' => \App\Enums\DecisionParticipantRole::PARTICIPANT->value,
            ]);
        }
    }
}
