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
        app(\App\Services\ConfigService::class)->applyMailConfig();

        // Simple raw string email for V1
        $subject = "Dazo Notification: " . str_replace('_', ' ', $this->eventType->value);
        $body = "Hello {$this->user->name},\n\nYou have a new notification: {$this->eventType->value}.\n";
        
        if (!empty($this->payload)) {
            $body .= "Details:\n" . json_encode($this->payload, JSON_PRETTY_PRINT);
        }

        Mail::raw($body, function ($message) use ($subject) {
            $message->to($this->user->email)
                    ->subject($subject);
        });
    }
}
