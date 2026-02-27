<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'features';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get all translations for the feature.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FeatureLang::class, 'feature_id');
    }

    /**
     * Get feature IDs sorted by sort_order.
     *
     * @param  array<int, int>  $featureIds
     * @return \Illuminate\Support\Collection<int, int>
     */
    public static function getIdsSorted(array $featureIds): \Illuminate\Support\Collection
    {
        if ($featureIds === []) {
            return collect();
        }

        return static::query()
            ->whereIn('id', $featureIds)
            ->orderBy('sort_order')
            ->pluck('id');
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
     * Values belonging to this feature.
     */
    public function values(): HasMany
    {
        return $this->hasMany(FeatureValue::class, 'feature_id');
    }

    /**
     * Get the products for the feature.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'feature_product', 'feature_id', 'product_id')
            ->withPivot('feature_value_id');
    }

    /**
     * Get feature options with their values formatted for forms.
     *
     * @return array<int, array{id: int, name: string, values: array<int, array{id: int, value: string}>>>
     */
    public static function getOptionsWithValues(int $languageId): array
    {
        $features = static::query()
            ->with([
                'translations' => fn ($q) => $q->where('language_id', $languageId),
                'values.translations' => fn ($q) => $q->where('language_id', $languageId),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $features->map(static function (self $feature) use ($languageId): array {
            return [
                'id' => $feature->id,
                'name' => $feature->translations->firstWhere('language_id', $languageId)?->name ?? '',
                'values' => $feature->values->map(static function (FeatureValue $value) use ($languageId): array {
                    return [
                        'id' => $value->id,
                        'value' => $value->translations->firstWhere('language_id', $languageId)?->value ?? '',
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Feature $feature) {
            $feature->values()->delete();
        });
    }
}
