<?php

namespace App\Services;

use App\Models\InstanceConfig;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    private const CACHE_KEY = 'instance_config';

    /**
     * Retrieve all configurations.
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return InstanceConfig::all()->pluck('value', 'key')->toArray();
        });
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
            $this->set($key, $value);
        }
    }
}
