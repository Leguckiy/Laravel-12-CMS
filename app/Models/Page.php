<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Get all translations for this page.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PageLang::class, 'page_id');
    }

    /**
     * Get translation for specific language.
     */
    public function translation(?int $languageId = null): ?PageLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Page $page) {
            $page->translations()->delete();
        });
    }
}
