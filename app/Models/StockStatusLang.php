<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockStatusLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'stock_status_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = ['stock_status_id', 'language_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'stock_status_id',
        'language_id',
        'name',
    ];
}
