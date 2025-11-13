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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Feature $feature) {
            $feature->values->each(function(FeatureValue $featureValues) {
                $featureValues->translations()->delete();
            });
            $feature->values()->delete();
            $feature->translations()->delete();
        });
    }
}
