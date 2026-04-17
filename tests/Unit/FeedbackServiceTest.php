<?php

namespace Tests\Unit;

use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Circle;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeVersion(): DecisionVersion
    {
        $user = \App\Models\User::factory()->create();
        $circle = Circle::create([
            'name' => 'Cercle Test',
            'type' => 'open',
        ]);

        $decision = Decision::create([
            'circle_id' => $circle->id,
            'title' => 'Décision Test',
            'status' => \App\Enums\DecisionStatus::OBJECTION->value,
            'visibility' => 'public',
            'priority' => 0,
            'emergency_mode' => false,
        ]);

        return DecisionVersion::create([
            'decision_id' => $decision->id,
            'author_id' => $user->id,
            'version_number' => 1,
            'is_current' => true,
            'content' => 'Version de travail',
        ]);
    }

    public function test_has_no_blocking_objections_returns_true_when_empty()
    {
        $version = $this->makeVersion();
        $service = app(FeedbackService::class);

        $this->assertTrue($service->hasNoBlockingObjections($version));
    }

    public function test_has_no_blocking_objections_returns_false_when_open_objection_exists()
    {
        $version = $this->makeVersion();
        
        Feedback::create([
            'decision_version_id' => $version->id,
            'author_id' => \App\Models\User::factory()->create()->id,
            'type' => FeedbackType::OBJECTION->value,
            'status' => FeedbackStatus::SUBMITTED->value,
            'content' => 'Objection ouverte',
        ]);

        $service = app(FeedbackService::class);
        $this->assertFalse($service->hasNoBlockingObjections($version));
    }
    
    public function test_has_no_blocking_objections_returns_true_when_objection_is_withdrawn()
    {
        $version = $this->makeVersion();
        
        Feedback::create([
            'decision_version_id' => $version->id,
            'author_id' => \App\Models\User::factory()->create()->id,
            'type' => FeedbackType::OBJECTION->value,
            'status' => FeedbackStatus::WITHDRAWN->value,
            'content' => 'Objection retirée',
        ]);

        $service = app(FeedbackService::class);
        $this->assertTrue($service->hasNoBlockingObjections($version));
    }
}
