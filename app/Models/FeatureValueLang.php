<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureValueLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'feature_value_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = ['feature_value_id', 'language_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'feature_value_id',
        'language_id',
        'value',
    ];

    /**
     * Feature value relation.
     */
    public function value(): BelongsTo
    {
        return $this->belongsTo(FeatureValue::class, 'feature_value_id');
    }

    /**
     * Language relation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
