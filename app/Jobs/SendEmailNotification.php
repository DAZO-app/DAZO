<?php

namespace App\Jobs;

use App\Enums\NotificationEventType;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public NotificationEventType $eventType,
        public array $payload = []
    ) {}

    public function handle(): void
    {
        /** @var \App\Services\ConfigService $configService */
        $configService = app(\App\Services\ConfigService::class);
        $configService->applyMailConfig();

        $eventTypeKey = match ($this->eventType) {
            NotificationEventType::NEW_DECISION => 'new_decision',
            NotificationEventType::NEW_VERSION => 'new_version',
            NotificationEventType::PHASE_CHANGE => 'phase_change',
            NotificationEventType::DECISION_ADOPTED => 'decision_adopted',
            NotificationEventType::DECISION_ABANDONED => 'decision_rejected',
            NotificationEventType::PARTICIPANT_REMINDER => 'reminder',
            NotificationEventType::OBJECTION_SUBMITTED,
            NotificationEventType::SUGGESTION_SUBMITTED,
            NotificationEventType::FEEDBACK_REPLIED,
            NotificationEventType::FEEDBACK_JOINED => 'feedback',
            NotificationEventType::INVITATION => 'invitation',
            default => null
        };

        if (!$eventTypeKey || $configService->get("mail_{$eventTypeKey}_enabled") !== 'true') {
            // Log or handle disabled notification
            return;
        }

        $subject = $configService->get("mail_{$eventTypeKey}_subject");
        $bodyTemplate = $configService->get("mail_{$eventTypeKey}_body");
        $wrapper = $configService->get('mail_template_wrapper');

        // Prepare variables
        $baseUrl = config('app.url') ?: url('/');
        $vars = array_merge([
            '{name}' => $this->user->name,
            '{user_name}' => $this->user->name,
            '{url}' => $baseUrl,
            '{link}' => $baseUrl,
            '{site_link}' => $baseUrl,
        ], $this->payload);

        // Special handling for decision URL if decision_id is present
        if (isset($this->payload['decision_id'])) {
            $vars['{url}'] = $baseUrl . '/decisions/' . $this->payload['decision_id'];
            $vars['{link}'] = $vars['{url}'];
        }

        // Apply variables to subject and body
        foreach ($vars as $key => $val) {
            if (is_scalar($val)) {
                $subject = str_replace($key, (string)$val, $subject);
                $bodyTemplate = str_replace($key, (string)$val, $bodyTemplate);
            }
        }

        // Render with wrapper
        $logo = $configService->get('app_logo');
        $logoUrl = $logo ? (str_starts_with($logo, 'http') ? $logo : $baseUrl . '/storage/' . $logo) : $baseUrl . '/favicon.ico';
        
        $finalBody = str_replace(
            ['{message}', '{logo}', '{site_link}'],
            [$bodyTemplate, $logoUrl, $baseUrl],
            $wrapper
        );

        Mail::html($finalBody, function ($message) use ($subject) {
            $message->to($this->user->email)
                    ->subject($subject);
        });
    }
}
