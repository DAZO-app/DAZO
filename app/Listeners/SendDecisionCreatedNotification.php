<?php

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\DecisionCreated;
use App\Services\NotificationService;

class SendDecisionCreatedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(DecisionCreated $event): void
    {
        $decision = $event->decision;
        $circle = $decision->circle;

        // Notify all circle members except the author
        foreach ($circle->members()->with('user')->get() as $member) {
            if ($member->user_id === $decision->participants()->where('role', 'author')->first()?->user_id) {
                continue;
            }
            $this->notificationService->notify(
                $member->user,
                NotificationEventType::NEW_DECISION,
                ['decision_id' => $decision->id, 'title' => $decision->title, 'circle' => $circle->name]
            );
        }
    }
}
