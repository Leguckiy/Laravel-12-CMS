<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    public const IMAGE_DIRECTORY = 'user';

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_group_id',
        'language_id',
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
     * Handle model events such as cleanup on delete.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->image_path) {
                Storage::disk(config('media.disk'))->delete($user->image_path);
            }
        });
    }

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

    /**
     * Get the language selected by the user.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    /**
     * Get storage path for the user's avatar relative to the disk root.
     */
    public function getImagePathAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return self::IMAGE_DIRECTORY . '/' . $this->image;
    }

    /**
     * Get absolute URL to the user's avatar on the configured disk.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(config('media.disk'));

        if (!$disk->exists($this->image_path)) {
            return null;
        }

        return $disk->url($this->image_path);
    }
}
