<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
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
     * Get translation for a specific language.
     */
    public function translation(int $languageId): ?CustomerGroupLang
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (CustomerGroup $customerGroup) {
            $customerGroup->translations()->delete();
        });
    }
}
