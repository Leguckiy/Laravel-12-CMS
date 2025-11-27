<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * Retrieve a setting value by its name.
     */
    public static function get(string $name, ?string $default = null): ?string
    {
        return self::query()
            ->where('name', $name)
            ->value('value') ?? $default;
    }

    /**
     * Create or update a setting value.
     */
    public static function set(string $name, ?string $value): self
    {
        return self::query()->updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }

    /**
     * Delete a setting by its name.
     */
    public static function deleteByName(string $name): void
    {
        self::query()->where('name', $name)->delete();
    }

    /**
     * Get all translations for this category.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SettingLang::class, 'setting_id');
    }
}
