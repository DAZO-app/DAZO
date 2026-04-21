<?php

namespace App\Mail;

use App\Models\Circle;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CircleInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invitation $invitation,
        public Circle $circle
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invitation à rejoindre le cercle '{$this->circle->name}' sur DAZO",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.circle_invitation',
            with: [
                'acceptUrl' => config('app.url') . "/invitations/{$this->invitation->token}",
            ],
        );
    }
}
