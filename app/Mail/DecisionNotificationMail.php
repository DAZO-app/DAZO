<?php
namespace App\Mail;

use App\Models\Decision;
use App\Models\DecisionVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DecisionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public \App\Models\Decision $decision,
        public \App\Models\DecisionVersion $version,
        public \App\Models\User $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Demande de décision [" . $this->decision->title . "]",
        );
    }

    public function content(): Content
    {
        $magicLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'magic.login',
            now()->addDays(7),
            [
                'user' => $this->recipient->id,
                'redirect' => '/decisions/' . $this->decision->id
            ]
        );

        return new Content(
            view: 'emails.decision_notification',
            with: [
                'magicLink' => $magicLink,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
