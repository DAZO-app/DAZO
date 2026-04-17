<?php

namespace App\Services;

use App\Enums\NotificationCategory;
use App\Enums\NotificationEventType;
use App\Jobs\SendEmailNotification;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;

class NotificationService
{
    /**
     * Notify a user about an event
     */
    public function notify(User $user, NotificationEventType $eventType, array $payload = []): void
    {
        $category = $this->determineCategory($eventType);

        // Fetch preference, default to true if not set
        $preference = NotificationPreference::firstOrCreate(
            ['user_id' => $user->id, 'category' => $category->value],
            ['email_enabled' => true, 'web_enabled' => true]
        );

        if ($preference->web_enabled) {
            Notification::create([
                'user_id' => $user->id,
                'category' => $category,
                'event_type' => $eventType,
                'payload' => $payload,
            ]);
        }

        if ($preference->email_enabled) {
            SendEmailNotification::dispatch($user, $eventType, $payload);
        }
    }

    private function determineCategory(NotificationEventType $eventType): NotificationCategory
    {
        return match ($eventType) {
            NotificationEventType::NEW_DECISION,
            NotificationEventType::NEW_VERSION,
            NotificationEventType::DECISION_ADOPTED,
            NotificationEventType::DECISION_ADOPTED_OVERRIDE,
            NotificationEventType::DECISION_ABANDONED,
            NotificationEventType::DECISION_LAPSED,
            NotificationEventType::DECISION_DESERTED => NotificationCategory::LIFECYCLE,

            NotificationEventType::OBJECTION_SUBMITTED,
            NotificationEventType::SUGGESTION_SUBMITTED,
            NotificationEventType::FEEDBACK_REPLIED,
            NotificationEventType::FEEDBACK_JOINED => NotificationCategory::FEEDBACK,

            NotificationEventType::PARTICIPANT_REMINDER => NotificationCategory::VOTE,

            NotificationEventType::ANIMATOR_INVOKED,
            NotificationEventType::EMAIL_VALIDATION,
            NotificationEventType::INVITATION => NotificationCategory::SYSTEM,
        };
    }
}
