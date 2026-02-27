<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockStatus extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'stock_statuses';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Get all translations for this stock status.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(StockStatusLang::class, 'stock_status_id');
    }

    /**
     * Get all products that use this stock status.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'stock_status_id');
    }

    /**
     * Get all names for different languages.
     *
     * @return array [language_id => name]
     */
    public function getNames(): array
    {
        return $this->translations()->pluck('name', 'language_id')->toArray();
    }

    /**
     * Get name for specific language.
     */
    public function getName(int $languageId): ?string
    {
        return $this->translations()->where('language_id', $languageId)->value('name');
    }

    /**
     * Get stock statuses as options for selects in a specific language.
     *
     * @return array<int, array{id: int, name: string}>
     */
    public static function getOptions(int $languageId): array
    {
        return static::query()
            ->with('translations')
            ->orderBy('id')
            ->get()
            ->map(static function (self $status) use ($languageId): array {
                return [
                    'id' => $status->id,
                    'name' => $status->translations
                        ->firstWhere('language_id', $languageId)?->name ?? '',
                ];
            })
            ->values()
            ->toArray();
    }
}
