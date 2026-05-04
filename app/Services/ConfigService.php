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
        $defaults = $this->defaults();

        $fromDb = Cache::rememberForever(self::CACHE_KEY, function () {
            return InstanceConfig::all()->pluck('value', 'key')->toArray();
        });

        $merged = array_merge($defaults, $fromDb);
        
        // Ensure critical fields like mail subjects/bodies/wrapper are not empty if they exist in defaults
        foreach ($defaults as $key => $val) {
            if (str_starts_with($key, 'mail_') && (empty($merged[$key]) || $merged[$key] === '')) {
                $merged[$key] = $val;
            }
        }

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

    public function defaults(): array
    {
        return [
            'app_name'              => 'DAZO',
            'app_logo'              => null,
            'decision_reaction_days' => '3',
            'decision_objection_days' => '3',
            'decision_revision_months' => '6',
            'reminder_hours_before' => '24',
            'public_registration'   => 'true',
            'mail_sender_name'      => 'DAZO Notifications',
            'mail_contact_address'  => 'contact@dazo.app',
            'maintenance_mode'      => 'false',
            // Publication API Publique & Front
            'enable_public_front'   => 'true',
            'require_admin_approval'=> 'false',
            'allowed_domains'       => '',
            'recaptcha_site_key'    => '',
            'recaptcha_secret_key'  => '',
            'public_circles'        => '[]',
            'public_categories'     => '[]',
            'public_statuses'       => '[]',
            'public_filters'        => '[]',
            'public_api_key'        => '',
            
            // Pages de contenu (Content stored as HTML)
            'page_legal_content'    => '<h1>Mentions Légales</h1><p>Contenu par défaut à personnaliser...</p>',
            'page_privacy_content'  => '<h1>Politique de Confidentialité</h1><p>Contenu par défaut à personnaliser...</p>',
            'page_terms_content'    => '<h1>Conditions Générales d\'Utilisation</h1><p>Contenu par défaut à personnaliser...</p>',
            
            // Mail Templates Subjects & Bodies
            'mail_new_decision_enabled' => 'true',
            'mail_new_decision_subject' => 'Nouvelle proposition de décision',
            'mail_new_decision_body'    => '<h1>Nouvelle décision</h1><p>Bonjour {name},</p><p>Une nouvelle proposition "{title}" a été publiée.</p><p><a href="{url}">Voir la décision</a></p>',
            
            'mail_phase_change_enabled' => 'true',
            'mail_phase_change_subject' => 'Changement de Phase',
            'mail_phase_change_body'    => '<h1>Changement de phase</h1><p>Bonjour {name},</p><p>La décision "{title}" est passée en nouvelle phase : {phase}.</p><p><a href="{url}">Voir la décision</a></p>',
            
            'mail_reminder_enabled'     => 'true',
            'mail_reminder_subject'     => 'Rappel : Action requise sur une décision',
            'mail_reminder_body'        => '<h1>Rappel échéance</h1><p>Bonjour {name},</p><p>Une action est attendue de votre part sur "{title}". Échéance : {deadline}.</p><p><a href="{url}">Accéder à la décision</a></p>',
            
            'mail_decision_adopted_enabled' => 'true',
            'mail_decision_adopted_subject' => 'Une décision a été adoptée',
            'mail_decision_adopted_body'    => '<h1>Décision adoptée !</h1><p>Bonjour {name},</p><p>La proposition "{title}" a été officiellement adoptée.</p><p><a href="{url}">Voir le résultat</a></p>',
            
            'mail_decision_rejected_enabled' => 'true',
            'mail_decision_rejected_subject' => 'Une décision n\'a pas été adoptée',
            'mail_decision_rejected_body'    => '<h1>Décision refusée</h1><p>Bonjour {name},</p><p>La proposition "{title}" n\'a pas recueilli le consensus nécessaire.</p><p><a href="{url}">Voir les détails</a></p>',

            'mail_invitation_enabled'   => 'true',
            'mail_invitation_subject'   => "📩 Invitation à rejoindre le cercle '{circle}'",
            'mail_invitation_body'      => "<h1>Invitation au cercle</h1><p>Bonjour,</p><p>Vous avez été invité à rejoindre le cercle <strong>{circle}</strong> par <strong>{inviter}</strong>.</p><p>Description : {description}</p><p><a href=\"{url}\">Accepter l'invitation</a></p>",
            
            // SMTP Settings
            'mail_host'             => '',
            'mail_port'             => '1025',
            'mail_username'         => '',
            'mail_password'         => '',
            'mail_encryption'       => 'null',
            
            // OAuth Providers (Matches AdminConfig.vue key + _client_id)
            'google_client_id'      => '',
            'google_client_secret'  => '',
            'github_client_id'      => '',
            'github_client_secret'  => '',
            'facebook_client_id'    => '',
            'facebook_client_secret' => '',
            'twitter_client_id'     => '',
            'twitter_client_secret' => '',
            'linkedin_client_id'    => '',
            'linkedin_client_secret' => '',
            'microsoft_client_id'   => '',
            'microsoft_client_secret' => '',
            'franceconnect_client_id' => '',
            'franceconnect_client_secret' => '',

            // TEMPLATE GÉNÉRAL EMAILS
            'mail_template_logo'            => '',
            'mail_template_site_link'       => 'https://dazo.app',
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
      </div>
    </div>
  </div>
</div>',
        ];
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
