<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    public const IMAGE_DIRECTORY = 'categories';

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
            if ($category->image) {
                Storage::disk(config('media.disk'))->delete($category->image_path);
            }
            $category->translations()->delete();
        });
    }

    public function getImagePathAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return self::IMAGE_DIRECTORY . '/' . $this->image;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return Storage::disk(config('media.disk'))->url($this->image_path);
    }
}
