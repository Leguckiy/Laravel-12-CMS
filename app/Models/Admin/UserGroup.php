<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroup extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'permission',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'permission' => 'array',
    ];

    /**
     * Get the users for the user group.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_group_id');
    }
}
