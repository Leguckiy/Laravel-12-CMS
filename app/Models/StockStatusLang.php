<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockStatusLang extends Model
{
    protected $table = 'stock_statuses_lang';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stock_status_id',
        'language_id',
        'name',
    ];
}
