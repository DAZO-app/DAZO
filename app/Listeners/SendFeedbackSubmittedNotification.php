<?php

namespace App\Listeners;

use App\Enums\NotificationEventType;
use App\Events\FeedbackSubmitted;
use App\Services\NotificationService;

class SendFeedbackSubmittedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(FeedbackSubmitted $event): void
    {
        $feedback = $event->feedback;
        $decision = $feedback->version->decision;
        $eventType = $feedback->type->value === 'objection'
            ? NotificationEventType::OBJECTION_SUBMITTED
            : NotificationEventType::SUGGESTION_SUBMITTED;

        // Notify decision author and animator
        foreach ($decision->participants()->with('user')->get() as $participant) {
            if ($participant->user_id === $feedback->author_id) continue;
            $this->notificationService->notify(
                $participant->user,
                $eventType,
                ['decision_id' => $decision->id, 'feedback_id' => $feedback->id, 'type' => $feedback->type->value]
            );
        }
    }
}
