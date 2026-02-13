<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'order_status_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_status_id',
        'language_id',
        'name',
    ];
}
