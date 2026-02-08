<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'code',
        'symbol_left',
        'symbol_right',
        'decimal_place',
        'value',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Convert amount from base currency (value=1) to this currency.
     * Price in DB is in base currency. value = rate to base (1 base = value of this currency).
     */
    public function convertFromBase(float|string $amount): float
    {
        return (float) $amount * (float) $this->value;
    }

    /**
     * Convert amount from this (current) currency to base currency.
     */
    public function convertToBase(float|string $amountInCurrent): float
    {
        $rate = (float) $this->value;
        if ($rate <= 0) {
            return 0.0;
        }

        return (float) $amountInCurrent / $rate;
    }

    /**
     * Convert price from base currency and format with symbol_left/symbol_right.
     */
    public function formatPriceFromBase(float|string $price): string
    {
        return $this->formatPrice($this->convertFromBase($price));
    }

    /**
     * Format price with currency symbol_left and symbol_right from DB.
     */
    public function formatPrice(float|string $price): string
    {
        $price = (float) $price;
        $decimals = (int) ($this->decimal_place ?? 2);
        $left = $this->symbol_left ?? '';
        $right = $this->symbol_right ?? '';

        $formatted = number_format($price, $decimals);

        return $left . $formatted . $right;
    }
}
