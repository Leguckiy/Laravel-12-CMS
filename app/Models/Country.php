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
     * 
     * @param int $languageId
     * @return string|null
     */
    public function getName(int $languageId): ?string
    {
        return $this->translations()->where('language_id', $languageId)->value('name');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Country $country) {
            $country->translations()->delete();
        });
    }
}
