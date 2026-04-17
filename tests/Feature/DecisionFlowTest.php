<?php

namespace Tests\Feature;

use App\Enums\CircleMemberRole;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Circle;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DecisionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_status_update_adopts_decision_when_no_blocking_objection_remains()
    {
        $author = User::factory()->create();
        $animator = User::factory()->create();
        $member = User::factory()->create();

        $circle = Circle::factory()->create([
            'name' => 'Collectif Produit',
        ]);

        $circle->members()->createMany([
            ['user_id' => $author->id, 'role' => CircleMemberRole::ANIMATOR],
            ['user_id' => $animator->id, 'role' => CircleMemberRole::ANIMATOR],
            ['user_id' => $member->id, 'role' => CircleMemberRole::MEMBER],
        ]);

        $decision = Decision::factory()->create([
            'circle_id' => $circle->id,
            'title' => 'Adopter une nouvelle charte',
            'status' => DecisionStatus::OBJECTION,
        ]);

        $version = DecisionVersion::factory()->create([
            'decision_id' => $decision->id,
            'author_id' => $author->id,
            'is_current' => true,
        ]);

        $decision->participants()->createMany([
            ['user_id' => $author->id, 'role' => DecisionParticipantRole::AUTHOR],
            ['user_id' => $animator->id, 'role' => DecisionParticipantRole::ANIMATOR],
        ]);

        $feedback = Feedback::factory()->create([
            'decision_version_id' => $version->id,
            'author_id' => $member->id,
            'type' => FeedbackType::OBJECTION,
            'status' => FeedbackStatus::SUBMITTED,
        ]);

        Sanctum::actingAs($animator);

        $response = $this->putJson("/api/v1/decisions/{$decision->id}/feedback/{$feedback->id}/status", [
            'status' => FeedbackStatus::TREATED->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('feedback.status', FeedbackStatus::TREATED->value);

        $this->assertEquals(DecisionStatus::ADOPTED, $decision->fresh()->status);
    }

    public function test_feedback_endpoint_is_scoped_to_the_given_decision(): void
    {
        $user = User::factory()->create();

        $circle = Circle::factory()->create();

        $circle->members()->create([
            'user_id' => $user->id,
            'role' => CircleMemberRole::ANIMATOR,
        ]);

        $decisionA = Decision::factory()->create([
            'circle_id' => $circle->id,
            'status' => DecisionStatus::OBJECTION,
        ]);

        DecisionVersion::factory()->create([
            'decision_id' => $decisionA->id,
            'author_id' => $user->id,
            'is_current' => true,
        ]);

        $decisionB = Decision::factory()->create([
            'circle_id' => $circle->id,
            'status' => DecisionStatus::OBJECTION,
        ]);

        $versionB = DecisionVersion::factory()->create([
            'decision_id' => $decisionB->id,
            'author_id' => $user->id,
            'is_current' => true,
        ]);

        $feedback = Feedback::factory()->create([
            'decision_version_id' => $versionB->id,
            'author_id' => $user->id,
            'type' => FeedbackType::OBJECTION,
            'status' => FeedbackStatus::SUBMITTED,
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/decisions/{$decisionA->id}/feedback/{$feedback->id}")
            ->assertNotFound();
    }
}
