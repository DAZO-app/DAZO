<?php

namespace App\Services;

use App\Models\InstanceConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ConfigService
{
    private const CACHE_KEY = 'instance_config';

    /**
     * Retrieve all configurations with defaults.
     */
    public function all(): array
    {
        $defaults = [
            'app_name'              => 'DAZO',
            'app_logo'              => null,
            'decision_reaction_days' => '3',
            'decision_objection_days' => '3',
            'reminder_hours_before' => '24',
            'public_registration'   => 'true',
            'mail_sender_name'      => 'DAZO Notifications',
            'mail_contact_address'  => 'contact@dazo.app',
            'maintenance_mode'      => 'false',
            // Publication API Publique & Front
            'enable_public_front'   => 'true',
            'require_admin_approval'=> 'false',
            'recaptcha_site_key'    => '',
            'recaptcha_secret_key'  => '',
            'public_circles'        => '[]',
            'public_categories'     => '[]',
            'public_statuses'       => '[]',
            'public_filters'        => '[]',
            'public_api_key'        => '',
            'legal_mentions_url'    => '',
            'privacy_policy_url'    => '',
            'terms_of_service_url'  => '',
            // Pages de contenu (Title, Slug, Content)
            'page_legal_title'      => 'Mentions Légales',
            'page_legal_slug'       => 'mentions-legales',
            'page_legal_content'    => '<h1>Mentions Légales</h1><p>Contenu par défaut à personnaliser...</p>',
            'page_privacy_title'    => 'Politique de Confidentialité',
            'page_privacy_slug'     => 'politique-confidentialite',
            'page_privacy_content'  => '<h1>Politique de Confidentialité</h1><p>Contenu par défaut à personnaliser...</p>',
            'page_terms_title'      => 'Conditions Générales d\'Utilisation',
            'page_terms_slug'       => 'cgu',
            'page_terms_content'    => '<h1>Conditions Générales d\'Utilisation</h1><p>Contenu par défaut à personnaliser...</p>',
            // Pièces jointes — laisser vide pour accepter tous les types non-bloqués
            'allowed_file_types'    => '',
            'max_file_size_mb'      => '10',
            'mail_host'             => '',
            'mail_port'             => '587',
            'mail_username'         => '',
            'mail_password'         => '',
            'mail_encryption'       => 'tls',
            // Sujets et corps des emails par défaut
            'mail_reminder_subject'     => "⚠️ Rappel : La décision '{title}' arrive à échéance",
            'mail_reminder_body'        => "Bonjour {name},\n\nCeci est un rappel concernant la décision : **{title}**.\n\nLa phase actuelle (**{phase}**) arrive bientôt à échéance. Votre participation est attendue afin de faire progresser le processus.\n\n**Échéance :** {deadline}\n\nMerci de votre contribution.",
            'mail_extended_subject'     => "⏳ Prolongation : La décision '{title}' a été prolongée",
            'mail_extended_body'        => "Bonjour {name},\n\nL'échéance de la phase de **{phase}** pour la décision **{title}** a été prolongée.\n\nVotre participation est toujours requise.\n\n**Nouvelle échéance :** {deadline}\n\nMerci de votre contribution.",
            'mail_invitation_subject'   => "📩 Invitation à rejoindre le cercle '{circle}'",
            'mail_invitation_body'      => "Bonjour,\n\nVous avez été invité à rejoindre le cercle **{circle}** sur la plateforme DAZO par **{inviter}**.\n\nCe cercle traite des sujets suivants : {description}\n\n[Accepter l'invitation]({url})",
            'mail_notification_subject' => "📢 Nouvelle étape pour : {title}",
            'mail_notification_body'    => "Bonjour {name},\n\nLa décision **{title}** vient de passer en phase de **{phase}**.\n\nVous pouvez consulter les détails et participer ici : [Voir la décision]({url})",
            'mail_contact_subject'      => "✉️ Nouveau message de contact : {subject}",
            'mail_contact_body'         => "Nom : {name}\nEmail : {email}\n\nMessage :\n{message}",
            
            // Configuration OAuth
            'auth_google_enabled'       => 'false',
            'auth_google_client_id'     => '',
            'auth_google_client_secret' => '',
            'auth_github_enabled'       => 'false',
            'auth_github_client_id'     => '',
            'auth_github_client_secret' => '',
            'auth_microsoft_enabled'    => 'false',
            'auth_microsoft_client_id'  => '',
            'auth_microsoft_client_secret' => '',
            'auth_facebook_enabled'     => 'false',
            'auth_facebook_client_id'   => '',
            'auth_facebook_client_secret' => '',
            'auth_apple_enabled'        => 'false',
            'auth_apple_client_id'      => '',
            'auth_apple_client_secret'  => '',
            'auth_franceconnect_enabled' => 'false',
            'auth_franceconnect_client_id' => '',
            'auth_franceconnect_client_secret' => '',

            // TEMPLATE GÉNÉRAL EMAILS
            'mail_template_logo'            => '', // URL ou path vers le logo
            'mail_template_logo_perso'      => '', // Deuxième logo
            'mail_template_site_link'       => 'https://dazo.app',
            'mail_template_site_link_register' => 'https://dazo.app/register',
            'mail_template_wrapper'         => '<div style="font-family: \'Inter\', sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="padding: 32px; text-align: center; border-bottom: 1px solid #f1f5f9;">
      <a href="{site_link}">
        <img src="{logo}" alt="Logo" style="max-height: 48px; width: auto;">
      </a>
    </div>
    <div style="padding: 40px; line-height: 1.6; font-size: 16px;">
      {message}
    </div>
    <div style="padding: 32px; background: #f8fafc; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #f1f5f9;">
      <p style="margin: 0 0 16px 0;">Vous recevez cet email car vous participez à la gouvernance sur notre plateforme.</p>
      <div style="display: flex; justify-content: center; gap: 16px;">
        <a href="{site_link}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Notre Site</a>
        <span style="color: #cbd5e1;">&bull;</span>
        <a href="{site_link_register}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">S\'inscrire</a>
      </div>
    </div>
  </div>
</div>',
        ];

        $fromDb = Cache::rememberForever(self::CACHE_KEY, function () {
            return InstanceConfig::all()->pluck('value', 'key')->toArray();
        });

        $merged = array_merge($defaults, $fromDb);
        
        // Decode JSON arrays for specific keys so the frontend gets arrays
        $jsonKeys = ['public_circles', 'public_categories', 'public_statuses', 'public_filters'];
        foreach ($jsonKeys as $key) {
            if (isset($merged[$key]) && is_string($merged[$key])) {
                $decoded = json_decode($merged[$key], true);
                if (is_array($decoded)) {
                    $merged[$key] = $decoded;
                } else {
                    $merged[$key] = [];
                }
            }
        }

        return $merged;
    }

    /**
     * Get a specific configuration key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $configs = $this->all();
        return $configs[$key] ?? $default;
    }

    /**
     * Set a configuration key.
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        InstanceConfig::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Bulk update configs.
     */
    public function setMultiple(array $configs): void
    {
        foreach ($configs as $key => $value) {
            $val = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            if (is_array($val)) {
                $val = json_encode($val);
            }
            $this->set($key, $val);
        }
    }

    /**
     * Upload and set the app logo.
     */
    public function uploadLogo(UploadedFile $file): string
    {
        // Delete old logo if exists
        $oldLogo = $this->get('app_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $file->store('branding', 'public');
        $this->set('app_logo', $path);

        return $path;
    }

    /**
     * Apply SMTP configuration to Laravel at runtime.
     */
    public function applyMailConfig(): void
    {
        $configs = $this->all();

        if (!empty($configs['mail_host'])) {
            config([
                'mail.mailers.smtp.host'       => $configs['mail_host'],
                'mail.mailers.smtp.port'       => $configs['mail_port'] ?? 587,
                'mail.mailers.smtp.username'   => $configs['mail_username'],
                'mail.mailers.smtp.password'   => $configs['mail_password'],
                'mail.mailers.smtp.encryption' => $configs['mail_encryption'] === 'null' ? null : $configs['mail_encryption'],
                'mail.from.address'            => $configs['mail_contact_address'] ?? config('mail.from.address'),
                'mail.from.name'               => $configs['mail_sender_name'] ?? config('mail.from.name'),
            ]);
        }
    }
}
