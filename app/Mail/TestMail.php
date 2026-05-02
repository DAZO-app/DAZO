<?php

namespace App\Mail;

use App\Services\ConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customSubject,
        public string $customBody
    ) {
        app(ConfigService::class)->applyMailConfig();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject,
        );
    }

    public function content(): Content
    {
        $configService = app(ConfigService::class);
        $wrapper = $configService->get('mail_template_wrapper');
        
        $logo = $configService->get('mail_template_logo');
        if (strpos($logo, 'http') !== 0 && $logo) {
            $logo = rtrim(config('app.url'), '/') . '/' . ltrim($logo, '/');
        }

        $logo_perso = $configService->get('mail_template_logo_perso');
        if (strpos($logo_perso, 'http') !== 0 && $logo_perso) {
            $logo_perso = rtrim(config('app.url'), '/') . '/' . ltrim($logo_perso, '/');
        }

        $replacements = [
            '{logo}'               => $logo,
            '{logo_perso}'         => $logo_perso,
            '{site_link}'          => $configService->get('mail_template_site_link'),
            '{site_link_register}' => $configService->get('mail_template_site_link_register'),
            '{message}'            => $this->customBody,
        ];

        $html = strtr($wrapper, $replacements);

        return new Content(
            htmlString: $html,
        );
    }
}
