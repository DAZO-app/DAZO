<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $subjectLine;
    public $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct(User $sender, string $subjectLine, string $messageBody)
    {
        $this->sender = $sender;
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[DAZO Contact] " . $this->subjectLine,
            replyTo: [
                new Address($this->sender->email, $this->sender->name),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.admin',
            with: [
                'userName' => $this->sender->name,
                'userEmail' => $this->sender->email,
                'subjectLine' => $this->subjectLine,
                'messageBody' => $this->messageBody,
            ],
        );
    }
}
