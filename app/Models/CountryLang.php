<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'country_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country_id',
        'language_id',
        'name',
    ];
}
