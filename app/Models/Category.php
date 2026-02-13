<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * Find active category by slug for the given language.
     */
    public static function findBySlug(string $slug, int $languageId): self
    {
        return static::query()
            ->where('status', true)
            ->whereHas('translations', function ($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                    ->where('slug', $slug);
            })
            ->with(['translations'])
            ->firstOrFail();
    }

    /**
     * Get translation for specific language.
     * Uses loaded relation when available to avoid N+1.
     */
    public function translation(int $languageId): ?CategoryLang
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('language_id', $languageId);
        }

        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
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
        });
    }

    public function getImagePathAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return self::IMAGE_DIRECTORY.'/'.$this->image;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk(config('media.disk'))->url($this->image_path);
    }
}
