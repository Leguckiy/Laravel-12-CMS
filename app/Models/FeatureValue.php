<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureValue extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'feature_values';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'feature_id',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'feature_id' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Feature relation.
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    /**
     * Products that have this feature value (through feature_product pivot).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'feature_product', 'feature_value_id', 'product_id');
    }

    /**
     * Group value IDs by feature_id: [featureId => [valueId, ...]].
     *
     * @param  array<int, int>  $valueIds
     * @return array<int, array<int, int>>
     */
    public static function getGroupsFromIds(array $valueIds): array
    {
        if ($valueIds === []) {
            return [];
        }
        $byFeature = static::query()
            ->whereIn('id', $valueIds)
            ->pluck('feature_id', 'id');
        $groups = [];
        foreach ($byFeature as $valueId => $featureId) {
            $groups[(int) $featureId][] = (int) $valueId;
        }

        return $groups;
    }

    /**
     * Get feature values for filter with counts (for given product IDs and language).
     *
     * @param  array<int, int>  $valueIds
     * @param  array<int, int>  $productIds
     * @return \Illuminate\Database\Eloquent\Collection<int, FeatureValue>
     */
    public static function forFilterWithCounts(array $valueIds, array $productIds, int $languageId): \Illuminate\Database\Eloquent\Collection
    {
        if ($valueIds === []) {
            return collect();
        }

        return static::query()
            ->whereIn('id', $valueIds)
            ->withCount(['products as products_count' => fn ($q) => $q->whereIn('product_id', $productIds)])
            ->with(['feature' => fn ($q) => $q->with(['translations' => fn ($t) => $t->where('language_id', $languageId)])])
            ->with(['translations' => fn ($q) => $q->where('language_id', $languageId)])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Translations for the value.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FeatureValueLang::class, 'feature_value_id');
    }

    /**
     * Translation for one value.
     */
    public function translation(int $languageId): ?FeatureValueLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (FeatureValue $value) {
            $value->translations()->delete();
        });
    }
}
