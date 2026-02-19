<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'countries';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'iso_code_2',
        'iso_code_3',
        'postcode_required',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'postcode_required' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this stock status.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CountryLang::class, 'country_id');
    }

    /**
     * Get all names for different languages.
     *
     * @return array [language_id => name]
     */
    public function getNames(): array
    {
        return $this->translations()->pluck('name', 'language_id')->toArray();
    }

    /**
     * Get name for specific language.
     */
    public function getName(int $languageId): ?string
    {
        return $this->translations()->where('language_id', $languageId)->value('name');
    }

    /**
     * Whether postcode is required for the given country (for validation).
     */
    public static function isPostcodeRequired(?int $countryId): bool
    {
        if ($countryId === null || $countryId === 0) {
            return false;
        }

        $country = static::query()->find($countryId);

        return $country !== null && $country->postcode_required;
    }

    /**
     * Get active countries as option arrays for checkout/forms.
     * Each option: id, name (for given language), postcode_required.
     *
     * @return array<int, array{id: int, name: string, postcode_required: bool}>
     */
    public static function getOptionsForCheckout(int $languageId): array
    {
        $countries = static::query()
            ->where('status', true)
            ->with(['translations' => fn ($q) => $q->where('language_id', $languageId)])
            ->orderBy('id')
            ->get();

        $options = [];
        foreach ($countries as $country) {
            $translation = $country->translations->firstWhere('language_id', $languageId);
            $options[] = [
                'id' => $country->id,
                'name' => $translation->name,
                'postcode_required' => (bool) $country->postcode_required,
            ];
        }

        return $options;
    }
}
