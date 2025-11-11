<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
