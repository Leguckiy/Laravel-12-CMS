<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Setting;
use App\Models\SettingLang;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::all()->pluck('id', 'code');

        // Simple settings (non-multilingual)
        $simpleSettings = [
            'config_name' => 'My Shop',
            'config_owner' => 'Shop Owner',
            'config_address' => '123 Main Street, City, Country',
            'config_email' => 'info@myshop.com',
            'config_telephone' => '+380501234567',
            'config_open' => 'Mon-Fri: 9:00-18:00',
            'config_country_id' => '201',
            'config_language_id' => '1',
            'config_currency_id' => '3',
        ];

        foreach ($simpleSettings as $name => $value) {
            Setting::create([
                'name' => $name,
                'value' => $value,
            ]);
        }

        // Image settings (empty values)
        $imageSettings = [
            'config_logo' => null,
            'config_icon' => null,
        ];

        foreach ($imageSettings as $name => $value) {
            Setting::create([
                'name' => $name,
                'value' => $value,
            ]);
        }

        // Multilingual settings
        $multilangSettings = [
            'config_meta_title' => [
                'en' => 'My Shop - Online Store',
                'uk' => 'Мій Магазин - Інтернет-магазин',
            ],
            'config_meta_description' => [
                'en' => 'Welcome to our online store. We offer a wide range of products at competitive prices.',
                'uk' => 'Ласкаво просимо до нашого інтернет-магазину. Ми пропонуємо широкий асортимент товарів за конкурентними цінами.',
            ],
        ];

        foreach ($multilangSettings as $settingName => $translations) {
            $setting = Setting::create([
                'name' => $settingName,
                'value' => null,
            ]);

            foreach ($translations as $languageCode => $value) {
                $languageId = $languages[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                SettingLang::create([
                    'setting_id' => $setting->id,
                    'language_id' => $languageId,
                    'value' => $value,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
