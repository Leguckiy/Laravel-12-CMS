<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'cart_token',
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

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public static function findByToken(string $cartToken): ?self
    {
        return static::query()
            ->where('cart_token', $cartToken)
            ->with(['items'])
            ->first();
    }

    public static function findForCustomer(int $customerId): ?self
    {
        return static::query()
            ->where('customer_id', $customerId)
            ->with(['items'])
            ->first();
    }

    public function attachToCustomer(int $customerId): void
    {
        $this->customer_id = $customerId;
        $this->save();
    }

    public function syncToken(string $cartToken): void
    {
        $this->cart_token = $cartToken;
        $this->save();
    }

    public function unbindToken(): void
    {
        $this->cart_token = null;
        $this->save();
    }

    public static function unbindTokenForCustomer(int $customerId): void
    {
        $cart = static::query()->where('customer_id', $customerId)->first();
        if ($cart !== null) {
            $cart->unbindToken();
        }
    }

    public static function deleteForCustomer(int $customerId): void
    {
        static::query()->where('customer_id', $customerId)->delete();
    }
}
