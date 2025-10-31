<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockStatus extends Model
{
    protected $table = 'stock_statuses';
    
    public $timestamps = false;
    
    protected $fillable = [];

    /**
     * Get all translations for this stock status.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(StockStatusLang::class, 'stock_status_id');
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
     * 
     * @param int $languageId
     * @return string|null
     */
    public function getName(int $languageId): ?string
    {
        return $this->translations()->where('language_id', $languageId)->value('name');
    }
}
