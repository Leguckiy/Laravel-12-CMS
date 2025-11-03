<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLang extends Model
{
    protected $table = 'countries_lang';
    
    public $timestamps = false;
    
    protected $fillable = [
        'country_id',
        'language_id',
        'name',
    ];
}
