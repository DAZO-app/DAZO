<?php

namespace App\Services;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Collection;

class DecisionParticipationService
{
    /**
     * Get the list of user IDs eligible to participate in a decision.
     * (Circle members excluding observers, and excluding decision author, animator, and explicitly excluded members)
     */
    public function getEligibleUserIds(Decision $decision): array
    {
        $circle = $decision->circle;
        if (!$circle) return [];

        $allMemberIds = $circle->members()
            ->where('role', '!=', CircleMemberRole::OBSERVER->value)
            ->pluck('user_id')->toArray();

        $managingIds = $decision->participants()
            ->whereIn('role', [
                DecisionParticipantRole::EXCLUDED->value,
                DecisionParticipantRole::AUTHOR->value,
                DecisionParticipantRole::ANIMATOR->value,
            ])->pluck('user_id')->toArray();

        return array_values(array_diff($allMemberIds, $managingIds));
    }

    /**
     * Returns a query for decisions where the user is eligible for participation.
     */
    public function getEligibleDecisionsQuery(User $user): \Illuminate\Database\Eloquent\Builder
    {
        return Decision::whereHas('circle.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('role', '!=', CircleMemberRole::OBSERVER->value);
            })
            ->whereDoesntHave('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('role', [
                      DecisionParticipantRole::AUTHOR->value,
                      DecisionParticipantRole::EXCLUDED->value,
                  ]);
            });
    }

    /**
     * Returns a query for feedbacks where action is pending for the user.
     */
    public function getPendingFeedbacksQuery(User $user, array $feedbackTypes, string $phase): \Illuminate\Database\Eloquent\Builder
    {
        $terminal = [
            \App\Enums\FeedbackStatus::WITHDRAWN->value,
            \App\Enums\FeedbackStatus::ACKNOWLEDGED->value,
            \App\Enums\FeedbackStatus::REJECTED->value,
            \App\Enums\FeedbackStatus::TREATED->value,
        ];

        return Feedback::whereIn('type', $feedbackTypes)
            ->whereNotIn('status', $terminal)
            ->whereHas('version.decision', fn($q) => $q->where('status', $phase))
            ->where(function ($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhereHas('version.decision.participants', function ($q2) use ($user) {
                      $q2->where('user_id', $user->id)
                         ->whereIn('role', [
                             DecisionParticipantRole::AUTHOR->value,
                             DecisionParticipantRole::ANIMATOR->value,
                         ]);
                  });
            })
            ->whereHas('messages', function ($q) use ($user) {
                // Version sécurisée pour PostgreSQL : on vérifie que le message de l'auteur n'est pas le dernier
                // Mais pour faire simple et performant, on peut juste vérifier s'il existe un message qui n'est pas du user
                // En réalité, le besoin est : "le dernier message n'est pas de moi"
                $q->where('author_id', '!=', $user->id)
                  ->whereRaw('created_at = (select max(created_at) from feedback_messages as fm where fm.feedback_id = feedbacks.id)');
            });
    }

    /**
     * Get IDs of users who have already participated in the current version for a given phase config.
     */
    public function getParticipatedUserIds(DecisionVersion $version, array $phaseConfig): array
    {
        $hasFeedback = Feedback::where('decision_version_id', $version->id)
            ->whereIn('type', $phaseConfig['feedback_types'])
            ->pluck('author_id')->toArray();

        $hasConsent = Consent::where('decision_version_id', $version->id)
            ->whereIn('signal', $phaseConfig['consent_signals'])
            ->pluck('user_id')->toArray();

        return array_unique(array_merge($hasFeedback, $hasConsent));
    }

    /**
     * Get the stats of participation for a version.
     */
    public function getParticipationStats(Decision $decision, DecisionVersion $version, ?string $statusOverride = null): array
    {
        $eligible = $this->getEligibleUserIds($decision);
        $statusEnum = DecisionStatus::tryFrom($statusOverride ?? $decision->status->value);
        $phaseConfig = $statusEnum?->getPhaseConfig();

        $participatedCount = 0;
        if ($phaseConfig) {
            $participatedIds = $this->getParticipatedUserIds($version, $phaseConfig);
            $participatedCount = count(array_intersect($eligible, $participatedIds));
        }

        return [
            'eligible'     => count($eligible),
            'participated' => $participatedCount,
            'pending'      => max(0, count($eligible) - $participatedCount),
        ];
    }

    /**
     * Get the list of users who haven't participated yet in the active phase.
     */
    public function getPendingUsers(Decision $decision): Collection
    {
        $statusEnum = $decision->status;
        $phaseConfig = $statusEnum->getPhaseConfig();
        if (!$phaseConfig) return collect([]);

        $version = $decision->currentVersion;
        if (!$version) return collect([]);

        $eligibleIds = $this->getEligibleUserIds($decision);
        $participatedIds = $this->getParticipatedUserIds($version, $phaseConfig);
        $pendingIds = array_diff($eligibleIds, $participatedIds);

        return User::whereIn('id', $pendingIds)
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);
    }

    /**
     * Build the participation map for all phases of a version.
     */
    public function getPhaseParticipationMap(Decision $decision, DecisionVersion $version): array
    {
        $map = ['clarification' => [], 'reaction' => [], 'objection' => []];

        $versionFeedbacks = $version->feedbacks()->get(['author_id', 'type']);
        $versionConsents  = $version->consents()->get(['user_id', 'signal', 'phase']);

        foreach ($versionFeedbacks as $fb) {
            $typeVal = is_object($fb->type) ? $fb->type->value : $fb->type;
            match ($typeVal) {
                FeedbackType::CLARIFICATION->value => $map['clarification'][$fb->author_id] = $typeVal,
                FeedbackType::REACTION->value      => $map['reaction'][$fb->author_id] = $typeVal,
                FeedbackType::OBJECTION->value,
                FeedbackType::SUGGESTION->value    => $map['objection'][$fb->author_id] = $typeVal,
                default => null,
            };
        }

        foreach ($versionConsents as $cs) {
            $signalVal = is_object($cs->signal) ? $cs->signal->value : $cs->signal;
            $phaseVal  = is_object($cs->phase) ? $cs->phase->value : $cs->phase;
            
            if (in_array($phaseVal, [DecisionStatus::ADOPTED->value, DecisionStatus::ADOPTED_OVERRIDE->value])) {
                $phaseVal = 'objection';
            }

            if (isset($map[$phaseVal])) {
                $map[$phaseVal][(string)$cs->user_id] = $signalVal;
            }
        }

        return $map;
    }

    /**
     * Record automatic abstentions for a phase that is ending.
     */
    public function recordAbstentionsForPhase(Decision $decision, array $phaseConfig, string $phase): void
    {
        $version = $decision->currentVersion;
        if (!$version) return;

        $eligibleIds = $this->getEligibleUserIds($decision);
        
        // Use a slightly different logic here because we might need specifically 
        // the consents of THIS phase if we want to be very precise,
        // but generally we check if they participated at all in this phase.
        $hasFeedback = Feedback::where('decision_version_id', $version->id)
            ->whereIn('type', $phaseConfig['feedback_types'])
            ->pluck('author_id')->toArray();

        $hasConsent = Consent::where('decision_version_id', $version->id)
            ->where('phase', $phase)
            ->pluck('user_id')->toArray();

        $alreadyParticipated = array_unique(array_merge($hasFeedback, $hasConsent));
        $silentIds = array_diff($eligibleIds, $alreadyParticipated);

        foreach ($silentIds as $uid) {
            Consent::create([
                'decision_version_id' => $version->id,
                'user_id' => $uid,
                'signal' => ConsentSignal::ABSTENTION,
                'phase' => $phase,
            ]);
        }
    }

    /**
     * Calculate if the user needs to act on a decision.
     */
    public function getUserActionStatus(Decision $decision, $userId): array
    {
        $status = ['needs_action' => false, 'reason' => null];

        if (!$decision->status->isActivePhase()) {
            return $status;
        }

        $v = $decision->currentVersion;
        if (!$v) return $status;

        // User role
        $participants = $decision->relationLoaded('participants')
            ? $decision->participants
            : $decision->participants()->get();

        $myParticipant = $participants->firstWhere('user_id', $userId);
        $myRole = $myParticipant?->role->value ?? DecisionParticipantRole::PARTICIPANT->value;

        if ($myRole === DecisionParticipantRole::EXCLUDED->value) {
            return $status;
        }

        // Case A: Author/Animator needs to reply
        if (in_array($myRole, [DecisionParticipantRole::AUTHOR->value, DecisionParticipantRole::ANIMATOR->value])) {
            $feedbacks = $v->relationLoaded('feedbacks') ? $v->feedbacks : $v->feedbacks()->get();

            $needsReply = $feedbacks
                ->whereNotIn('status', [
                    \App\Enums\FeedbackStatus::WITHDRAWN->value,
                    \App\Enums\FeedbackStatus::ACKNOWLEDGED->value,
                    \App\Enums\FeedbackStatus::TREATED->value,
                ])
                ->contains(function ($fb) use ($userId) {
                    // On récupère le dernier message dans la collection déjà chargée
                    $lastMsg = $fb->messages->sortByDesc('created_at')->first();
                    return $lastMsg && $lastMsg->author_id !== $userId;
                });

            if ($needsReply) {
                $status['needs_action'] = true;
                $status['reason'] = 'reponse_attendue';
            }
            return $status;
        }

        // Case B: Participant needs to vote/feedback
        $phaseConfig = $decision->status->getPhaseConfig();
        if (!$phaseConfig) return $status;

        $feedbacks = $v->relationLoaded('feedbacks') ? $v->feedbacks : collect();
        $consents  = $v->relationLoaded('consents')  ? $v->consents  : collect();

        $hasFeedback = $feedbacks
            ->filter(fn($f) => $f->author_id === $userId && in_array(
                is_object($f->type) ? $f->type->value : $f->type,
                array_map(fn($t) => is_object($t) ? $t->value : $t, $phaseConfig['feedback_types'])
            ))
            ->isNotEmpty();

        $hasConsent = $consents
            ->filter(fn($c) => $c->user_id === $userId && in_array(
                is_object($c->signal) ? $c->signal->value : $c->signal,
                array_map(fn($s) => is_object($s) ? $s->value : $s, $phaseConfig['consent_signals'])
            ))
            ->isNotEmpty();

        if (!$hasFeedback && !$hasConsent) {
            $status['needs_action'] = true;
            $status['reason'] = 'participation_manquante';
        }

        return $status;
    }

    /**
     * Attach user action status to a collection of decisions.
     */
    public function attachUserActionStatus($decisions, $userId): void
    {
        if (!$userId || $decisions->isEmpty()) return;

        $decisions->loadMissing([
            'participants',
            'currentVersion.feedbacks.messages', // On charge tous les messages pour éviter le bug latestOfMany/MAX(uuid)
            'currentVersion.consents',
        ]);

        foreach ($decisions as $decision) {
            $decision->setAttribute('user_status', $this->getUserActionStatus($decision, $userId));
        }
    }
}
