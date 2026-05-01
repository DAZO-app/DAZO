<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Exception;

class TestEmail extends Command
{
    protected $signature = 'dazo:test-email {email : The recipient email address}';
    protected $description = 'Send a test email to validate SMTP configuration';

    public function handle()
    {
        $recipient = $this->argument('email');
        $this->info("Attempting to send a test email to: {$recipient}...");

        try {
            Mail::raw('Ceci est un mail de test pour valider la configuration SMTP de DAZO.', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('DAZO — Test de configuration SMTP');
            });

            $this->success("Email envoyé avec succès ! Veuillez vérifier la boîte de réception (et les spams) de {$recipient}.");
        } catch (Exception $e) {
            $this->error("L'envoi de l'email a échoué.");
            $this->line("Erreur : " . $e->getMessage());
            $this->line("");
            $this->info("Vérifiez votre fichier .env (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION).");
        }
    }

    protected function success($message)
    {
        $this->output->writeln("<info>✔</info> {$message}");
    }
}
