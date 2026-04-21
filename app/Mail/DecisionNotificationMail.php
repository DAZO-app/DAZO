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
        public Decision $decision,
        public DecisionVersion $version,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Demande de décision [" . $this->decision->title . "]",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.decision_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
