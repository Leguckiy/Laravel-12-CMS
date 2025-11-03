<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'image',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this category.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryLang::class, 'category_id');
    }

    /**
     * Get translation for specific language.
     * 
     * @param int $languageId
     * @return CategoryLang|null
     */
    public function translation(int $languageId): ?CategoryLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            $category->translations()->delete();
        });
    }
}
