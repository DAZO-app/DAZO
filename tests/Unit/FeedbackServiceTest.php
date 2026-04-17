<?php

namespace Tests\Unit;

use App\Enums\DecisionStatus;
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
        $circle = Circle::factory()->create();
        $decision = Decision::factory()->create([
            'circle_id' => $circle->id,
            'status' => DecisionStatus::OBJECTION,
        ]);

        return DecisionVersion::factory()->create([
            'decision_id' => $decision->id,
            'is_current' => true,
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
        
        Feedback::factory()->create([
            'decision_version_id' => $version->id,
            'type' => FeedbackType::OBJECTION,
            'status' => FeedbackStatus::SUBMITTED,
        ]);

        $service = app(FeedbackService::class);
        $this->assertFalse($service->hasNoBlockingObjections($version));
    }
    
    public function test_has_no_blocking_objections_returns_true_when_objection_is_withdrawn()
    {
        $version = $this->makeVersion();
        
        Feedback::factory()->create([
            'decision_version_id' => $version->id,
            'type' => FeedbackType::OBJECTION,
            'status' => FeedbackStatus::WITHDRAWN,
        ]);

        $service = app(FeedbackService::class);
        $this->assertTrue($service->hasNoBlockingObjections($version));
    }
}
