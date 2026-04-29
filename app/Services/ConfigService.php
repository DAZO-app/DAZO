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
            // Pièces jointes — laisser vide pour accepter tous les types non-bloqués
            'allowed_file_types'    => '',
            'max_file_size_mb'      => '10',
            'mail_host'             => '',
            'mail_port'             => '587',
            'mail_username'         => '',
            'mail_password'         => '',
            'mail_encryption'       => 'tls',
            'reminder_email_subject' => "⚠️ Rappel : La décision '{title}' arrive à échéance",
            'reminder_email_body'    => "Bonjour {name},\n\nCeci est un rappel concernant la décision : **{title}**.\n\nLa phase actuelle (**{phase}**) arrive bientôt à échéance. Votre participation est attendue afin de faire progresser le processus.\n\n**Échéance :** {deadline}\n\nMerci de votre contribution.",
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
