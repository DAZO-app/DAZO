<?php

namespace Tests\Unit;

use App\Enums\ConsentSignal;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackType;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour DecisionStatus::getPhaseConfig() et méthodes helpers.
 * Ces tests ne nécessitent PAS de base de données.
 */
class DecisionStatusTest extends TestCase
{
    // ──────────────────────────────────────────────────────────────────────
    // getPhaseConfig()
    // ──────────────────────────────────────────────────────────────────────

    public function test_clarification_phase_config_returns_correct_types(): void
    {
        $config = DecisionStatus::CLARIFICATION->getPhaseConfig();

        $this->assertNotNull($config);
        $this->assertSame([FeedbackType::CLARIFICATION->value], $config['feedback_types']);
        $this->assertSame([ConsentSignal::NO_QUESTIONS->value], $config['consent_signals']);
    }

    public function test_reaction_phase_config_returns_correct_types(): void
    {
        $config = DecisionStatus::REACTION->getPhaseConfig();

        $this->assertNotNull($config);
        $this->assertSame([FeedbackType::REACTION->value], $config['feedback_types']);
        $this->assertSame([ConsentSignal::NO_REACTION->value], $config['consent_signals']);
    }

    public function test_objection_phase_config_returns_correct_types(): void
    {
        $config = DecisionStatus::OBJECTION->getPhaseConfig();

        $this->assertNotNull($config);
        $this->assertContains(FeedbackType::OBJECTION->value, $config['feedback_types']);
        $this->assertContains(FeedbackType::SUGGESTION->value, $config['feedback_types']);
        $this->assertContains(ConsentSignal::NO_OBJECTION->value, $config['consent_signals']);
        $this->assertContains(ConsentSignal::ABSTENTION->value, $config['consent_signals']);
    }

    public function test_draft_returns_null_config(): void
    {
        $this->assertNull(DecisionStatus::DRAFT->getPhaseConfig());
    }

    public function test_adopted_returns_null_config(): void
    {
        $this->assertNull(DecisionStatus::ADOPTED->getPhaseConfig());
    }

    public function test_revision_returns_null_config(): void
    {
        $this->assertNull(DecisionStatus::REVISION->getPhaseConfig());
    }

    // ──────────────────────────────────────────────────────────────────────
    // isActivePhase()
    // ──────────────────────────────────────────────────────────────────────

    public function test_active_phases_are_correctly_identified(): void
    {
        $this->assertTrue(DecisionStatus::CLARIFICATION->isActivePhase());
        $this->assertTrue(DecisionStatus::REACTION->isActivePhase());
        $this->assertTrue(DecisionStatus::OBJECTION->isActivePhase());
    }

    public function test_non_active_phases_are_correctly_identified(): void
    {
        $this->assertFalse(DecisionStatus::DRAFT->isActivePhase());
        $this->assertFalse(DecisionStatus::REVISION->isActivePhase());
        $this->assertFalse(DecisionStatus::ADOPTED->isActivePhase());
        $this->assertFalse(DecisionStatus::ABANDONED->isActivePhase());
        $this->assertFalse(DecisionStatus::LAPSED->isActivePhase());
        $this->assertFalse(DecisionStatus::DESERTED->isActivePhase());
        $this->assertFalse(DecisionStatus::SUSPENDED->isActivePhase());
    }

    // ──────────────────────────────────────────────────────────────────────
    // isTerminal()
    // ──────────────────────────────────────────────────────────────────────

    public function test_terminal_statuses_are_correctly_identified(): void
    {
        $this->assertTrue(DecisionStatus::ADOPTED->isTerminal());
        $this->assertTrue(DecisionStatus::ADOPTED_OVERRIDE->isTerminal());
        $this->assertTrue(DecisionStatus::ABANDONED->isTerminal());
        $this->assertTrue(DecisionStatus::LAPSED->isTerminal());
        $this->assertTrue(DecisionStatus::DESERTED->isTerminal());
    }

    public function test_non_terminal_statuses_are_correctly_identified(): void
    {
        $this->assertFalse(DecisionStatus::DRAFT->isTerminal());
        $this->assertFalse(DecisionStatus::CLARIFICATION->isTerminal());
        $this->assertFalse(DecisionStatus::REACTION->isTerminal());
        $this->assertFalse(DecisionStatus::OBJECTION->isTerminal());
        $this->assertFalse(DecisionStatus::REVISION->isTerminal());
        $this->assertFalse(DecisionStatus::SUSPENDED->isTerminal());
    }
}
