<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'order_statuses';

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
        return $this->hasMany(OrderStatusLang::class, 'order_status_id');
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
     * Get orders with this status.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }

    /**
     * Get order history entries with this status.
     */
    public function orderHistories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_status_id');
    }
}
