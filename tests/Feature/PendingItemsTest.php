<?php

namespace Tests\Feature;

use App\Enums\CircleMemberRole;
use App\Enums\ConsentSignal;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Models\Circle;
use App\Models\Consent;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PendingItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reaction_phase_does_not_require_feedback_threads_to_be_defined(): void
    {
        [$user, $decision] = $this->createEligibleDecision(DecisionStatus::REACTION);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/pending-items?phase=reaction')
            ->assertOk()
            ->assertJsonCount(1, 'decisions')
            ->assertJsonPath('decisions.0.id', $decision->id);
    }

    public function test_pending_items_uses_decision_categories_relation(): void
    {
        [$user] = $this->createEligibleDecision(DecisionStatus::CLARIFICATION);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/pending-items?phase=clarification')
            ->assertOk()
            ->assertJsonStructure([
                'decisions' => [
                    '*' => ['id', 'categories', 'user_status'],
                ],
            ]);
    }

    public function test_decision_disappears_from_pending_items_after_phase_consent(): void
    {
        [$user, $decision, $version] = $this->createEligibleDecision(DecisionStatus::REACTION);

        Consent::create([
            'decision_version_id' => $version->id,
            'user_id' => $user->id,
            'signal' => ConsentSignal::NO_REACTION->value,
            'phase' => DecisionStatus::REACTION->value,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/pending-items?phase=reaction')
            ->assertOk()
            ->assertJsonCount(0, 'decisions');
    }

    private function createEligibleDecision(DecisionStatus $status): array
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $circle = Circle::factory()->create();

        $circle->members()->createMany([
            ['user_id' => $user->id, 'role' => CircleMemberRole::MEMBER->value],
            ['user_id' => $author->id, 'role' => CircleMemberRole::ANIMATOR->value],
        ]);

        $decision = Decision::factory()->create([
            'circle_id' => $circle->id,
            'status' => $status->value,
        ]);

        $version = DecisionVersion::factory()->create([
            'decision_id' => $decision->id,
            'author_id' => $author->id,
            'is_current' => true,
        ]);

        $decision->participants()->create([
            'user_id' => $author->id,
            'role' => DecisionParticipantRole::AUTHOR->value,
        ]);

        return [$user, $decision, $version];
    }
}
