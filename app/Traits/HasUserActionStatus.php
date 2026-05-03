<?php

namespace App\Traits;

use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Models\Decision;

trait HasUserActionStatus
{
    /**
     * Attache le statut d'action de l'utilisateur sur une collection de décisions.
     * Précharge les relations nécessaires pour éviter les requêtes N+1.
     */
    protected function attachUserActionStatus($decisions, $userId): void
    {
        if (!$userId || $decisions->isEmpty()) return;

        // Eager-load des relations nécessaires pour le calcul (évite N+1)
        $decisions->loadMissing([
            'participants',
            'currentVersion.feedbacks.latestMessage',
            'currentVersion.consents',
        ]);

        foreach ($decisions as $decision) {
            $decision->setAttribute('user_status', $this->calculateUserActionStatus($decision, $userId));
        }
    }

    /**
     * Calcule si l'utilisateur doit agir sur la décision.
     * Utilise exclusivement les relations déjà chargées (pas de requêtes supplémentaires).
     */
    protected function calculateUserActionStatus(Decision $decision, $userId): array
    {
        $status = ['needs_action' => false, 'reason' => null];

        if (!$decision->status->isActivePhase()) {
            return $status;
        }

        $v = $decision->currentVersion;
        if (!$v) return $status;

        // Rôle de l'utilisateur (via relation déjà chargée)
        $participants = $decision->relationLoaded('participants')
            ? $decision->participants
            : $decision->participants()->get();

        $myParticipant = $participants->firstWhere('user_id', $userId);
        $myRole = $myParticipant?->role->value ?? \App\Enums\DecisionParticipantRole::PARTICIPANT->value;

        if ($myRole === \App\Enums\DecisionParticipantRole::EXCLUDED->value) {
            return $status;
        }

        // Cas A : PORTEUR ou ANIMATEUR → agir si un thread attend une réponse
        if (in_array($myRole, [\App\Enums\DecisionParticipantRole::AUTHOR->value, \App\Enums\DecisionParticipantRole::ANIMATOR->value])) {
            $feedbacks = $v->relationLoaded('feedbacks')
                ? $v->feedbacks
                : $v->feedbacks()->get();

            $needsReply = $feedbacks
                ->whereNotIn('status', [
                    FeedbackStatus::WITHDRAWN->value,
                    FeedbackStatus::ACKNOWLEDGED->value,
                    FeedbackStatus::TREATED->value,
                ])
                ->contains(function ($fb) use ($userId) {
                    $lastMsg = $fb->latestMessage;
                    return $lastMsg && $lastMsg->author_id !== $userId;
                });

            if ($needsReply) {
                $status['needs_action'] = true;
                $status['reason'] = 'reponse_attendue';
            }
            return $status;
        }

        // Cas B : PARTICIPANT → agir s'il n'a pas encore participé à la phase
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
}
