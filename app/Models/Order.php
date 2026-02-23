<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'shipping_address_id',
        'firstname',
        'lastname',
        'email',
        'shipping_firstname',
        'shipping_lastname',
        'shipping_company',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_postcode',
        'shipping_country_id',
        'shipping_method',
        'shipping_cost',
        'payment_method',
        'subtotal',
        'total',
        'order_status_id',
        'language_id',
        'currency_id',
        'comment',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'shipping_method' => 'array',
            'shipping_cost' => 'decimal:4',
            'payment_method' => 'array',
            'subtotal' => 'decimal:4',
            'total' => 'decimal:4',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function shippingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'shipping_country_id');
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_id');
    }
}
