<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLang extends Model
{
    protected $table = 'order_statuses_lang';
    
    public $timestamps = false;
    
    protected $fillable = [
        'order_status_id',
        'language_id',
        'name',
    ];
}

