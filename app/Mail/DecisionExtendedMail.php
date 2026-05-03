<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DecisionExtendedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public \App\Models\Decision $decision,
        public \App\Models\User $user
    ) {
        app(\App\Services\ConfigService::class)->applyMailConfig();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $configService = app(\App\Services\ConfigService::class);
        $subjectTemplate = $configService->get('mail_extended_subject', "⏳ Prolongation : La décision '{title}' a été prolongée");
        
        $subject = str_replace('{title}', $this->decision->title, $subjectTemplate);

        $envelope = new Envelope(
            subject: $subject,
        );

        $senderName = $configService->get('mail_sender_name');
        $contactAddress = $configService->get('mail_contact_address');

        if ($senderName || $contactAddress) {
            $envelope->from(
                new \Illuminate\Mail\Mailables\Address(
                    $contactAddress ?? config('mail.from.address'),
                    $senderName ?? config('mail.from.name')
                )
            );
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $configService = app(\App\Services\ConfigService::class);
        $bodyTemplate = $configService->get('mail_extended_body');

        $replacements = [
            '{name}'     => $this->user->name,
            '{title}'    => $this->decision->title,
            '{phase}'    => $this->decision->status->value,
            '{deadline}' => $this->decision->current_deadline?->format('d/m/Y à H:i') ?? 'N/A',
            '{url}'      => config('app.url') . "/decisions/{$this->decision->id}"
        ];

        $body = strtr($bodyTemplate, $replacements);

        return new Content(
            markdown: 'emails.decisions.extended',
            with: [
                'body' => $body,
                'url'  => config('app.url') . "/decisions/{$this->decision->id}"
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
