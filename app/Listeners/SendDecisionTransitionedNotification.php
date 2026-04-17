<?php

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\DecisionTransitioned;
use App\Services\NotificationService;

class SendDecisionTransitionedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(DecisionTransitioned $event): void
    {
        $decision = $event->decision;

        foreach ($decision->circle->members()->with('user')->get() as $member) {
            $this->notificationService->notify(
                $member->user,
                NotificationEventType::NEW_DECISION,
                ['decision_id' => $decision->id, 'title' => $decision->title, 'status' => $decision->status->value]
            );
        }
    }
}
