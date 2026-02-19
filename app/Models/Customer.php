<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The table associated with the model.
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_group_id',
        'firstname',
        'lastname',
        'email',
        'telephone',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the customer group that owns the customer.
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * Get the customer's addresses (one customer can have many addresses).
     *
     * @return HasMany<Address, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    /**
     * Addresses for checkout (ordered by id). Country name for display
     * comes from controller (countryNames) in the template.
     */
    public function getAddressesForCheckout(): Collection
    {
        return $this->addresses()->orderBy('id')->get();
    }

    /**
     * Get the customer's full name.
     */
    public function getFullnameAttribute(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }
}
