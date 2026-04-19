<?php

namespace App\Traits;

use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Decision;

trait HasUserActionStatus
{
    /**
     * Attache le statut d'action de l'utilisateur sur une collection de décisions.
     */
    protected function attachUserActionStatus($decisions, $userId)
    {
        if (!$userId) return;

        foreach ($decisions as $decision) {
            $decision->setAttribute('user_status', $this->calculateUserActionStatus($decision, $userId));
        }
    }

    /**
     * Calcule si l'utilisateur doit agir sur la décision.
     */
    protected function calculateUserActionStatus(Decision $decision, $userId)
    {
        $status = [
            'needs_action' => false,
            'reason' => null
        ];

        $v = $decision->currentVersion;
        $decisionStatus = $decision->status->value;

        // On ne gère que les phases actives
        $activePhases = [
            DecisionStatus::CLARIFICATION->value,
            DecisionStatus::REACTION->value,
            DecisionStatus::OBJECTION->value,
        ];

        if (!in_array($decisionStatus, $activePhases) || !$v) {
            return $status;
        }

        // 1. Déterminer le rôle de l'utilisateur
        $participants = $decision->participants;
        $myParticipant = $participants->where('user_id', $userId)->first();
        $myRole = $myParticipant ? $myParticipant->role->value : DecisionParticipantRole::PARTICIPANT->value;

        // Si exclu, pas d'action
        if ($myRole === DecisionParticipantRole::EXCLUDED->value) {
            return $status;
        }

        // Cas A : L'utilisateur est PORTEUR ou ANIMATEUR
        // Il doit agir si un fil de discussion attend une réponse de sa part.
        if (in_array($myRole, [DecisionParticipantRole::AUTHOR->value, DecisionParticipantRole::ANIMATOR->value])) {
            $needsReply = $v->feedbacks()
                ->whereNotIn('status', [FeedbackStatus::WITHDRAWN->value, FeedbackStatus::ACKNOWLEDGED->value, FeedbackStatus::TREATED->value])
                ->get()
                ->contains(function ($fb) use ($userId) {
                    $lastMsg = $fb->messages()->latest()->first();
                    return $lastMsg && $lastMsg->author_id !== $userId;
                });

            if ($needsReply) {
                $status['needs_action'] = true;
                $status['reason'] = 'reponse_attendue';
            }
            return $status;
        }

        // Cas B : L'utilisateur est PARTICIPANT
        // Il doit agir s'il n'a pas encore participé à la phase actuelle.
        $phaseFeedbackTypes = [];
        $phaseConsentSignals = [];

        if ($decisionStatus === DecisionStatus::CLARIFICATION->value) {
            $phaseFeedbackTypes = [FeedbackType::CLARIFICATION->value];
            $phaseConsentSignals = [ConsentSignal::NO_QUESTIONS->value];
        } elseif ($decisionStatus === DecisionStatus::REACTION->value) {
            $phaseFeedbackTypes = [FeedbackType::REACTION->value];
            $phaseConsentSignals = [ConsentSignal::NO_REACTION->value];
        } elseif ($decisionStatus === DecisionStatus::OBJECTION->value) {
            $phaseFeedbackTypes = [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value];
            $phaseConsentSignals = [ConsentSignal::NO_OBJECTION->value, ConsentSignal::ABSTENTION->value];
        }

        $hasFeedback = $v->feedbacks()
            ->where('author_id', $userId)
            ->whereIn('type', $phaseFeedbackTypes)
            ->exists();

        $hasConsent = $v->consents()
            ->where('user_id', $userId)
            ->whereIn('signal', $phaseConsentSignals)
            ->exists();

        if (!$hasFeedback && !$hasConsent) {
            $status['needs_action'] = true;
            $status['reason'] = 'participation_manquante';
        }

        return $status;
    }
}
