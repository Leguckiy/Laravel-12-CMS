<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'page_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var array<int, string>
     */
    protected $primaryKey = ['page_id', 'language_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'page_id',
        'language_id',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
