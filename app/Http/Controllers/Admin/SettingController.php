<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomerGroup;
use App\Models\Setting;
use App\Models\SettingLang;
use App\Services\AdminImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends AdminController
{
    private const SHOP_IMAGE_DIRECTORY = 'shop';

    protected const SIMPLE_SETTINGS = [
        'config_name',
        'config_owner',
        'config_address',
        'config_email',
        'config_telephone',
        'config_open',
        'config_country_id',
        'config_language_id',
        'config_currency_id',
        'config_customer_group_id',
    ];

    protected const IMAGE_SETTINGS = [
        'config_logo',
        'config_icon',
    ];

    protected const MULTILANG_SETTINGS = [
        'config_meta_title',
        'config_meta_description',
    ];

    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'settings',
            'route' => 'admin.setting.edit',
        ],
    ];

    protected string $title = 'settings';

    /**
     * Show the settings edit form.
     */
    public function edit(): View
    {
        return view('admin.setting.edit', $this->prepareSettingFormData());
    }

    /**
     * Persist updated settings.
     */
    public function update(SettingRequest $request): RedirectResponse
    {
        $oldCurrencyId = Setting::get('config_currency_id');
        $newCurrencyId = $request->input('config_currency_id');

        // Simple (non-multilingual) settings
        foreach (self::SIMPLE_SETTINGS as $name) {
            $value = $request->input($name);

            Setting::set($name, (string) $value);
        }

        // Recalculate currency values if default currency changed
        if ($oldCurrencyId !== $newCurrencyId && $newCurrencyId) {
            $this->recalculateCurrencyValues((int) $oldCurrencyId, (int) $newCurrencyId);
        }

        // Image settings
        // Logo: no resize, so width/height not needed
        $this->handleImageSetting(
            $request,
            'config_logo',
            self::SHOP_IMAGE_DIRECTORY,
            null,
            null,
            false
        );

        $this->handleImageSetting(
            $request,
            'config_icon',
            self::SHOP_IMAGE_DIRECTORY,
            (int) config('image_sizes.small.width'),
            (int) config('image_sizes.small.height'),
            true
        );

        // Multilingual settings
        $multiLangSettings = [
            'config_meta_title' => $request->input('config_meta_title', []),
            'config_meta_description' => $request->input('config_meta_description', []),
        ];

        foreach ($multiLangSettings as $settingName => $translations) {
            $setting = Setting::firstOrCreate(['name' => $settingName]);

            // Remove all existing translations for this setting
            SettingLang::query()
                ->where('setting_id', $setting->id)
                ->delete();

            // Create translations for all values (including empty)
            foreach ($translations as $languageId => $value) {
                SettingLang::create([
                    'setting_id' => $setting->id,
                    'language_id' => (int) $languageId,
                    'value' => (string) $value,
                ]);
            }
        }

        return redirect()
            ->route('admin.setting.edit')
            ->with('success', __('admin.updated_successfully'));
    }

    /**
     * Prepare form data for settings edit form.
     */
    private function prepareSettingFormData(): array
    {
        $allSettings = Setting::with('translations')->get();

        $options = self::SIMPLE_SETTINGS;
        $optionsMultiLang = self::MULTILANG_SETTINGS;

        $settings = [];
        $translations = [];

        foreach ($allSettings as $setting) {
            if (! $setting->name) {
                continue;
            }

            if (in_array($setting->name, $options, true)) {
                $settings[$setting->name] = $setting->value;
            }

            if (in_array($setting->name, $optionsMultiLang, true)) {
                $translations[$setting->name] = $setting->translations
                    ->pluck('value', 'language_id')
                    ->toArray();
            }
        }

        // Image settings (store current paths, not just filenames)
        foreach (self::IMAGE_SETTINGS as $imageSetting) {
            $settings[$imageSetting] = Setting::get($imageSetting);
        }

        $languages = $this->getLanguages();

        $countriesOptions = Country::with('translations')
            ->get()
            ->map(function (Country $country) {
                return [
                    'id' => $country->id,
                    'name' => $this->translation($country->translations)?->name ?? '',
                ];
            })
            ->values()
            ->toArray();

        $languagesOptions = $languages
            ->map(function ($language) {
                return [
                    'id' => $language->id,
                    'name' => $language->name,
                ];
            })
            ->values()
            ->toArray();

        $currenciesOptions = Currency::all()
            ->map(function (Currency $currency) {
                return [
                    'id' => $currency->id,
                    'name' => $currency->title,
                ];
            })
            ->values()
            ->toArray();

        $customerGroupsOptions = CustomerGroup::with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (CustomerGroup $group) {
                return [
                    'id' => $group->id,
                    'name' => $this->translation($group->translations)?->name ?? (string) $group->id,
                ];
            })
            ->values()
            ->toArray();

        return compact(
            'settings',
            'translations',
            'languages',
            'countriesOptions',
            'languagesOptions',
            'currenciesOptions',
            'customerGroupsOptions',
        );
    }

    /**
     * Handle upload / removal of image-based settings.
     */
    private function handleImageSetting(
        SettingRequest $request,
        string $settingName,
        string $directory,
        ?int $width,
        ?int $height,
        bool $resize
    ): void {
        $uploader = new AdminImageUploader;

        // Use clean base filenames like "logo" / "icon" instead of full setting name
        $baseName = match ($settingName) {
            'config_logo' => 'logo',
            'config_icon' => 'icon',
            default => $settingName,
        };

        $currentPath = Setting::get($settingName);

        if ($request->boolean("{$settingName}_remove")) {
            $uploader->delete($currentPath);
            Setting::set($settingName, null);
            $currentPath = null;
        }

        if (! $request->hasFile($settingName)) {
            return;
        }

        if ($currentPath) {
            $uploader->delete($currentPath);
        }

        $filename = $uploader->uploadImage(
            $baseName,
            $directory,
            $request->file($settingName),
            $width,
            $height,
            $resize
        );

        $path = trim($directory, '/').'/'.$filename;

        Setting::set($settingName, $path);
    }

    /**
     * Recalculate currency values when default currency changes.
     * The new default currency gets value = 1, others are recalculated proportionally.
     */
    private function recalculateCurrencyValues(?int $oldCurrencyId, int $newCurrencyId): void
    {
        $newDefaultCurrency = Currency::find($newCurrencyId);

        if (! $newDefaultCurrency || ! $newDefaultCurrency->value) {
            return;
        }

        $newDefaultValue = (float) $newDefaultCurrency->value;

        // Calculate conversion factor: 1 / newDefaultValue
        // Example: if new default currency has value = 0.85, factor = 1 / 0.85 = 1.176
        // This means all currencies should be multiplied by 1.176
        $conversionFactor = 1.0 / $newDefaultValue;

        // Update all currencies
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            if ($currency->id === $newCurrencyId) {
                // New default currency gets value = 1
                $currency->update(['value' => 1.0]);
            } elseif ($currency->value) {
                // Other currencies are recalculated proportionally
                $newValue = (float) $currency->value * $conversionFactor;
                $currency->update(['value' => $newValue]);
            }
        }
    }
}
