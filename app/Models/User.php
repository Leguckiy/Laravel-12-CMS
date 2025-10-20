<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_group_id',
        'username',
        'password',
        'firstname',
        'lastname',
        'email',
        'image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the user's full name.
     */
    public function getFullnameAttribute(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * Get the user group that owns the user.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }
}
