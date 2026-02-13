<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'feature_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'feature_id',
        'language_id',
        'name',
    ];

    /**
     * Feature relation.
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    /**
     * Language relation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
