<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $table = 'shipping_methods';

    protected $fillable = [
        'code',
        'config',
        'countries',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'countries' => 'array',
            'status' => 'boolean',
        ];
    }
}
