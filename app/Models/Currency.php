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
}
