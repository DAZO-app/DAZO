<?php

namespace Tests\Feature;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Circle;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tests des compteurs d'actions en attente (PendingCountsController).
 * Vérifie la cohérence des compteurs pour les phases Clarification, Réaction, Objection.
 */
class PendingCountsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Circle $circle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->circle = Circle::factory()->create();

        // L'utilisateur est membre du cercle
        $this->circle->members()->create([
            'user_id' => $this->user->id,
            'role'    => CircleMemberRole::MEMBER->value,
        ]);

        Sanctum::actingAs($this->user);
    }

    // ──────────────────────────────────────────────────────────────────────
    // CLARIFICATION
    // ──────────────────────────────────────────────────────────────────────

    public function test_clarification_pending_when_user_has_not_participated(): void
    {
        $author  = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::CLARIFICATION, $author);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(1, $response->json('clarifications'));
    }

    public function test_clarification_not_pending_after_no_questions_consent(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::CLARIFICATION, $author);
        $version  = $decision->currentVersion;

        Consent::create([
            'decision_version_id' => $version->id,
            'user_id' => $this->user->id,
            'signal'  => ConsentSignal::NO_QUESTIONS->value,
        ]);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('clarifications'));
    }

    public function test_clarification_not_pending_after_submitting_clarification(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::CLARIFICATION, $author);
        $version  = $decision->currentVersion;

        Feedback::create([
            'decision_version_id' => $version->id,
            'author_id' => $this->user->id,
            'type'      => FeedbackType::CLARIFICATION->value,
            'status'    => FeedbackStatus::SUBMITTED->value,
            'content'   => 'Ma demande de clarification',
        ]);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('clarifications'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // RÉACTION
    // ──────────────────────────────────────────────────────────────────────

    public function test_reaction_pending_when_user_has_not_participated(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::REACTION, $author);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(1, $response->json('reactions'));
    }

    public function test_reaction_not_pending_after_no_reaction_consent(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::REACTION, $author);
        $version  = $decision->currentVersion;

        Consent::create([
            'decision_version_id' => $version->id,
            'user_id' => $this->user->id,
            'signal'  => ConsentSignal::NO_REACTION->value,
        ]);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('reactions'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // OBJECTION
    // ──────────────────────────────────────────────────────────────────────

    public function test_objection_pending_when_user_has_not_voted(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::OBJECTION, $author);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(1, $response->json('objections'));
    }

    public function test_objection_not_pending_after_no_objection_consent(): void
    {
        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::OBJECTION, $author);
        $version  = $decision->currentVersion;

        Consent::create([
            'decision_version_id' => $version->id,
            'user_id' => $this->user->id,
            'signal'  => ConsentSignal::NO_OBJECTION->value,
        ]);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('objections'));
    }

    public function test_observer_is_not_counted_as_pending(): void
    {
        $observer = User::factory()->create();
        $this->circle->members()->create([
            'user_id' => $observer->id,
            'role'    => CircleMemberRole::OBSERVER->value,
        ]);

        $author   = User::factory()->create();
        $decision = $this->createDecisionInPhase(DecisionStatus::CLARIFICATION, $author);

        Sanctum::actingAs($observer);
        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('clarifications'));
    }

    public function test_author_is_not_counted_as_pending_participant(): void
    {
        $decision = $this->createDecisionInPhase(DecisionStatus::CLARIFICATION, $this->user);

        $response = $this->getJson('/api/v1/pending-counts');

        $response->assertOk();
        $this->assertSame(0, $response->json('clarifications'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPER
    // ──────────────────────────────────────────────────────────────────────

    private function createDecisionInPhase(DecisionStatus $status, User $author): Decision
    {
        $decision = Decision::factory()->create([
            'circle_id' => $this->circle->id,
            'status'    => $status->value,
        ]);

        $version = DecisionVersion::factory()->create([
            'decision_id' => $decision->id,
            'author_id'   => $author->id,
            'is_current'  => true,
        ]);

        $decision->setRelation('currentVersion', $version);

        $decision->participants()->createMany([
            ['user_id' => $author->id, 'role' => DecisionParticipantRole::AUTHOR->value],
        ]);

        return $decision;
    }
}
