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

        // 1. Check Global Preferences
        $preference = NotificationPreference::firstOrCreate(
            ['user_id' => $user->id, 'category' => $category->value],
            ['email_enabled' => true, 'web_enabled' => true]
        );

        if (!$preference->email_enabled && !$preference->web_enabled) {
            return;
        }

        // 2. Check Per-Decision Settings if applicable
        if (isset($payload['decision_id'])) {
            $decisionSetting = \App\Models\DecisionUserSetting::where('user_id', $user->id)
                ->where('decision_id', $payload['decision_id'])
                ->first();

            if ($decisionSetting) {
                $level = $decisionSetting->notification_level;
                
                if ($level === \App\Enums\DecisionNotificationLevel::NONE) {
                    return;
                }

                if ($level === \App\Enums\DecisionNotificationLevel::PHASE_CHANGE && $category !== \App\Enums\NotificationCategory::PHASE_CHANGE) {
                    return;
                }

                if ($level === \App\Enums\DecisionNotificationLevel::RELEVANT) {
                    // Relevant means author, animator or directly involved
                    // We check if the user is a participant with a role other than observer
                    $isRelevant = \App\Models\DecisionParticipant::where('decision_id', $payload['decision_id'])
                        ->where('user_id', $user->id)
                        ->whereNotIn('role', [\App\Enums\DecisionParticipantRole::OBSERVER->value, \App\Enums\DecisionParticipantRole::EXCLUDED->value])
                        ->exists();
                    
                    if (!$isRelevant) {
                        return;
                    }
                }
            }
        }

        // 3. Web/Push Notification
        if ($preference->web_enabled) {
            Notification::create([
                'user_id' => $user->id,
                'category' => $category,
                'event_type' => $eventType,
                'payload' => $payload,
            ]);
            
            // Note: Here we would trigger actual Push (Firebase/OneSignal) if configured
        }

        // 4. Email Notification
        if ($preference->email_enabled) {
            SendEmailNotification::dispatch($user, $eventType, $payload);
        }
    }

    private function determineCategory(NotificationEventType $eventType): NotificationCategory
    {
        return match ($eventType) {
            NotificationEventType::NEW_DECISION => NotificationCategory::NEW_DECISION,
            
            NotificationEventType::NEW_VERSION => NotificationCategory::REVISION,
            
            NotificationEventType::PHASE_CHANGE,
            NotificationEventType::DECISION_ADOPTED,
            NotificationEventType::DECISION_ADOPTED_OVERRIDE,
            NotificationEventType::DECISION_ABANDONED,
            NotificationEventType::DECISION_LAPSED,
            NotificationEventType::DECISION_DESERTED => NotificationCategory::PHASE_CHANGE,

            NotificationEventType::OBJECTION_SUBMITTED,
            NotificationEventType::SUGGESTION_SUBMITTED,
            NotificationEventType::FEEDBACK_REPLIED,
            NotificationEventType::FEEDBACK_JOINED => NotificationCategory::FEEDBACK,

            NotificationEventType::PARTICIPANT_REMINDER => NotificationCategory::DEADLINE,

            NotificationEventType::ANIMATOR_INVOKED,
            NotificationEventType::EMAIL_VALIDATION,
            NotificationEventType::INVITATION => NotificationCategory::SYSTEM,
        };
    }
}
