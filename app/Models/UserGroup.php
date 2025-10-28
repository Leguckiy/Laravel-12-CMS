<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroup extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_groups';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

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
    
    /**
     * Check if user group has permission for given module title
     * 
     * @param string $title Module title (e.g., 'user/user')
     * @param string $type Permission type: 'view' or 'edit'
     */
    public function hasPermission(string $title, string $type): bool
    {
        $storedPermissions = $this->permission[$type] ?? [];
        
        return in_array($title, $storedPermissions);
    }

    /**
     * Check if user group has permission for given route name
     * 
     * @param string $routeName Route name (e.g., 'admin.user.index')
     * @param string $ability Permission ability: 'view' or 'edit'
     * @return bool
     */
    public function hasPermissionForRoute(string $routeName, string $ability): bool
    {
        // Extract module from route name (e.g., 'admin.user.index' => 'user')
        $parts = explode('.', $routeName);
        
        if (count($parts) < 3 || $parts[0] !== 'admin') {
            return false;
        }
        
        $module = $parts[1];
        
        // Get title from permissions mapping config
        $permissionsMapping = config('admin.permissions_mapping', []);
        $title = $permissionsMapping[$module] ?? null;
        
        if (!$title) {
            return false;
        }
        
        return $this->hasPermission($title, $ability);
    }
}
