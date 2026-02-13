<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'category_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'language_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
