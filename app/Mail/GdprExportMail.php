<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GdprExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $filePath;

    public function __construct($user, $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre export de données personnelles (RGPD) - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gdpr_export',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as('dazo-export-' . now()->format('Y-m-d') . '.json')
                ->withMime('application/json'),
        ];
    }
}
