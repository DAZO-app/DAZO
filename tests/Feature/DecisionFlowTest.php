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

        $circle = Circle::create([
            'name' => 'Collectif Produit',
            'type' => 'open',
        ]);

        $circle->members()->createMany([
            ['user_id' => $author->id, 'role' => CircleMemberRole::ANIMATOR->value],
            ['user_id' => $animator->id, 'role' => CircleMemberRole::ANIMATOR->value],
            ['user_id' => $member->id, 'role' => CircleMemberRole::MEMBER->value],
        ]);

        $decision = Decision::create([
            'circle_id' => $circle->id,
            'title' => 'Adopter une nouvelle charte',
            'status' => DecisionStatus::OBJECTION->value,
            'visibility' => 'public',
            'priority' => 0,
            'emergency_mode' => false,
        ]);

        $version = DecisionVersion::create([
            'decision_id' => $decision->id,
            'author_id' => $author->id,
            'version_number' => 1,
            'is_current' => true,
            'content' => 'Texte initial',
        ]);

        $decision->participants()->createMany([
            ['user_id' => $author->id, 'role' => DecisionParticipantRole::AUTHOR->value],
            ['user_id' => $animator->id, 'role' => DecisionParticipantRole::ANIMATOR->value],
        ]);

        $feedback = Feedback::create([
            'decision_version_id' => $version->id,
            'author_id' => $member->id,
            'type' => FeedbackType::OBJECTION->value,
            'status' => FeedbackStatus::SUBMITTED->value,
            'content' => 'Je vois un risque juridique.',
        ]);

        Sanctum::actingAs($animator);

        $response = $this->putJson("/api/v1/decisions/{$decision->id}/feedback/{$feedback->id}/status", [
            'status' => FeedbackStatus::TREATED->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('feedback.status', FeedbackStatus::TREATED->value);

        $this->assertSame(DecisionStatus::ADOPTED, $decision->fresh()->status);
    }

    public function test_feedback_endpoint_is_scoped_to_the_given_decision(): void
    {
        $user = User::factory()->create();

        $circle = Circle::create([
            'name' => 'Cercle Test',
            'type' => 'open',
        ]);

        $circle->members()->create([
            'user_id' => $user->id,
            'role' => CircleMemberRole::ANIMATOR->value,
        ]);

        $decisionA = Decision::create([
            'circle_id' => $circle->id,
            'title' => 'Decision A',
            'status' => DecisionStatus::OBJECTION->value,
            'visibility' => 'public',
            'priority' => 0,
            'emergency_mode' => false,
        ]);

        DecisionVersion::create([
            'decision_id' => $decisionA->id,
            'author_id' => $user->id,
            'version_number' => 1,
            'is_current' => true,
            'content' => 'Version A',
        ]);

        $decisionB = Decision::create([
            'circle_id' => $circle->id,
            'title' => 'Decision B',
            'status' => DecisionStatus::OBJECTION->value,
            'visibility' => 'public',
            'priority' => 0,
            'emergency_mode' => false,
        ]);

        $versionB = DecisionVersion::create([
            'decision_id' => $decisionB->id,
            'author_id' => $user->id,
            'version_number' => 1,
            'is_current' => true,
            'content' => 'Version B',
        ]);

        $feedback = Feedback::create([
            'decision_version_id' => $versionB->id,
            'author_id' => $user->id,
            'type' => FeedbackType::OBJECTION->value,
            'status' => FeedbackStatus::SUBMITTED->value,
            'content' => 'Hors périmètre',
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/decisions/{$decisionA->id}/feedback/{$feedback->id}")
            ->assertNotFound();
    }
}
