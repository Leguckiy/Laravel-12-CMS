<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'languages';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
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
     * Get active languages as simple options for selects.
     *
     * @return array<int, array{id: int, name: string}>
     */
    public static function getActiveOptions(): array
    {
        return static::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(static fn (self $language) => [
                'id' => $language->id,
                'name' => $language->name,
            ])
            ->values()
            ->toArray();
    }
}
