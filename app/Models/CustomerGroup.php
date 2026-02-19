<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    /**
     * Default group for new customer registration (from config or first by sort_order).
     * Returns null if no groups exist.
     */
    public static function getDefaultForRegistration(): ?self
    {
        $configId = (int) Setting::get('config_customer_group_id', '0');
        $group = $configId > 0 ? static::query()->find($configId) : null;

        return $group ?? static::query()->orderBy('sort_order')->orderBy('id')->first();
    }
    /**
     * The table associated with the model.
     */
    protected $table = 'customer_groups';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'approval',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approval' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all translations for the customer group.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CustomerGroupLang::class, 'customer_group_id');
    }

    /**
     * Get the customers for the customer group.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'customer_group_id');
    }

    /**
     * Get translation for a specific language.
     */
    public function translation(int $languageId): ?CustomerGroupLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }
}
