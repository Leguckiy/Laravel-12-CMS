<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'session_id',
        'customer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartProduct::class, 'cart_id');
    }

    public static function findForSession(string $sessionId): ?self
    {
        return static::query()
            ->where('session_id', $sessionId)
            ->with(['items'])
            ->first();
    }
}
