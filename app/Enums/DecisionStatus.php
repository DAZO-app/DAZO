<?php

namespace App\Enums;

enum DecisionStatus: string
{
    case DRAFT = 'draft';
    case CLARIFICATION = 'clarification';
    case REACTION = 'reaction';
    case OBJECTION = 'objection';
    case REVISION = 'revision';
    case ADOPTED = 'adopted';
    case ADOPTED_OVERRIDE = 'adopted_override';
    case ABANDONED = 'abandoned';
    case SUSPENDED = 'suspended';
    case LAPSED = 'lapsed';
    case DESERTED = 'deserted';

    /**
     * Renvoie la configuration des types de feedback et de signaux de consentement
     * associés à une phase donnée. Centralise la logique utilisée dans de nombreux
     * contrôleurs, services et traits.
     *
     * @return array{feedback_types: string[], consent_signals: string[]}|null
     *         null si la phase n'a pas de configuration (draft, adopted, etc.)
     */
    public function getPhaseConfig(): ?array
    {
        return match ($this) {
            self::CLARIFICATION => [
                'feedback_types'  => [FeedbackType::CLARIFICATION->value],
                'consent_signals' => [ConsentSignal::NO_QUESTIONS->value, ConsentSignal::ABSTENTION->value],
            ],
            self::REACTION => [
                'feedback_types'  => [FeedbackType::REACTION->value],
                'consent_signals' => [ConsentSignal::NO_REACTION->value, ConsentSignal::ABSTENTION->value],
            ],
            self::OBJECTION => [
                'feedback_types'  => [FeedbackType::OBJECTION->value, FeedbackType::SUGGESTION->value],
                'consent_signals' => [ConsentSignal::NO_OBJECTION->value, ConsentSignal::ABSTENTION->value],
            ],
            default => null,
        };
    }

    /**
     * Retourne true si ce statut correspond à une phase active (participation attendue).
     */
    public function isActivePhase(): bool
    {
        return $this->getPhaseConfig() !== null;
    }

    /**
     * Retourne true si la décision est dans un état terminal (plus d'action possible).
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::ADOPTED,
            self::ADOPTED_OVERRIDE,
            self::ABANDONED,
            self::LAPSED,
            self::DESERTED,
        ], true);
    }
}
