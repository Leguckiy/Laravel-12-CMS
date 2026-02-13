<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingLang extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'setting_lang';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = ['setting_id', 'language_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'setting_id',
        'language_id',
        'value',
    ];
}
