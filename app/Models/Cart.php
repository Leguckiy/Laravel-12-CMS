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

    public function syncSessionId(string $sessionId): void
    {
        $this->session_id = $sessionId;
        $this->save();
    }

    public function unbindSession(): void
    {
        $this->session_id = null;
        $this->save();
    }

    public static function unbindSessionForCustomer(int $customerId): void
    {
        $cart = static::query()->where('customer_id', $customerId)->first();
        if ($cart !== null) {
            $cart->unbindSession();
        }
    }

    public static function deleteForCustomer(int $customerId): void
    {
        static::query()->where('customer_id', $customerId)->delete();
    }
}
