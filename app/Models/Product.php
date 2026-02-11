<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /**
     * Directory name for product images in storage.
     */
    public const IMAGE_DIRECTORY = 'products';

    /**
     * The table associated with the model.
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reference',
        'quantity',
        'stock_status_id',
        'image',
        'price',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:4',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all translations for this product.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductLang::class, 'product_id');
    }

    /**
     * Find active product by slug for the given language, with relations for front show page.
     */
    public static function findForFrontShow(string $slug, int $languageId): self
    {
        return static::query()
            ->where('status', true)
            ->whereHas('translations', function ($query) use ($languageId, $slug): void {
                $query->where('language_id', $languageId)->where('slug', $slug);
            })
            ->with([
                'translations',
                'categories.translations' => fn ($q) => $q->where('language_id', $languageId),
                'stockStatus.translations' => fn ($q) => $q->where('language_id', $languageId),
                'features' => fn ($q) => $q->orderBy('sort_order'),
                'features.translations' => fn ($q) => $q->where('language_id', $languageId),
                'features.values' => fn ($q) => $q->orderBy('sort_order'),
                'features.values.translations' => fn ($q) => $q->where('language_id', $languageId),
            ])
            ->firstOrFail();
    }

    /**
     * Get translation for specific language.
     */
    public function translation(int $languageId): ?ProductLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * Get the stock status that owns the product.
     */
    public function stockStatus(): BelongsTo
    {
        return $this->belongsTo(StockStatus::class, 'stock_status_id');
    }

    /**
     * Get the features for the product.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_product', 'product_id', 'feature_id')
            ->withPivot('feature_value_id');
    }

    /**
     * Scope: filter products by feature values. Multiple values of same feature = OR, different features = AND.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<int, array<int, int>>  $featureValueGroups  [featureId => [valueId, ...]]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFeatureValueFilters($query, array $featureValueGroups)
    {
        if ($featureValueGroups === []) {
            return $query;
        }
        foreach ($featureValueGroups as $featureId => $valueIds) {
            $query->whereHas('features', function ($q) use ($featureId, $valueIds) {
                $q->where('features.id', $featureId)
                    ->whereIn('feature_product.feature_value_id', array_map('intval', $valueIds));
            });
        }

        return $query;
    }

    /**
     * Get the categories for the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    /**
     * Handle model events such as cleanup on delete.
     */
    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            if ($product->image) {
                Storage::disk(config('media.disk'))->delete($product->image_path);
            }
            $product->translations()->delete();
            $product->categories()->detach();
            $product->features()->detach();
        });
    }

    /**
     * Get storage path for the product's image relative to the disk root.
     */
    public function getImagePathAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return self::IMAGE_DIRECTORY.'/'.$this->image;
    }

    /**
     * Get absolute URL to the product's image on the configured disk.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(config('media.disk'));

        if (! $disk->exists($this->image_path)) {
            return null;
        }

        return $disk->url($this->image_path);
    }
}
