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
            'app_name' => 'DAZO',
            'app_logo' => null,
            'decision_reaction_days' => '3',
            'decision_objection_days' => '3',
            'reminder_hours_before' => '24',
            'public_registration' => 'true',
            'mail_sender_name' => 'DAZO Notifications',
            'mail_contact_address' => 'contact@dazo.app',
            'maintenance_mode' => 'false',
        ];

        $fromDb = Cache::rememberForever(self::CACHE_KEY, function () {
            return InstanceConfig::all()->pluck('value', 'key')->toArray();
        });

        return array_merge($defaults, $fromDb);
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
            // Handle booleans as strings "true"/"false" if needed, 
            // but the service treats everything as string in DB for now.
            $val = is_bool($value) ? ($value ? 'true' : 'false') : $value;
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
}
