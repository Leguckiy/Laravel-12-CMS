<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected const CACHE_KEY = 'settings_all';

    protected array $settings = [];

    public function __construct()
    {
        $this->load();
    }

    /**
     * Load settings from cache or DB into $settings
     */
    protected function load(): void
    {
        $this->settings = Cache::rememberForever(self::CACHE_KEY, function () {
            return $this->loadAllSettings();
        });
    }

    /**
     * Load all settings + multilingual values in a single array
     */
    protected function loadAllSettings(): array
    {
        $items = Setting::with('translations')->get();

        $result = [];

        foreach ($items as $setting) {
            $result[$setting->name] = [
                'value' => $setting->value,
                'lang'  => $setting->translations
                    ->pluck('value', 'language_id')
                    ->toArray(),
            ];
        }

        return $result;
    }

    /**
     * Get a setting value (with optional language support)
     */
    public function get(string $key, ?int $langId = null)
    {
        if (!isset($this->settings[$key])) {
            return null;
        }

        // If language ID is provided and translations exist
        if ($langId && !empty($this->settings[$key]['lang'])) {
            return $this->settings[$key]['lang'][$langId] ?? $this->settings[$key]['value'];
        }

        // Return default value
        return $this->settings[$key]['value'];
    }

    /**
     * Get all settings as an array
     */
    public function all(): array
    {
        return $this->settings;
    }

    /**
     * Clear cached settings and reload fresh data
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->load(); // reload from DB
    }
}
