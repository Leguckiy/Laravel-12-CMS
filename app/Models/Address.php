<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = 'addresses';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'firstname',
        'lastname',
        'company',
        'address_1',
        'address_2',
        'city',
        'postcode',
        'country_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Build shipping address array for checkout session.
     *
     * @return array<string, mixed>
     */
    public function toCheckoutSessionArray(): array
    {
        return ['address_id' => $this->id] + $this->only([
            'firstname',
            'lastname',
            'company',
            'address_1',
            'address_2',
            'city',
            'postcode',
            'country_id',
        ]);
    }

    /**
     * Single-line formatted address: "First Last, Address, City[, Country]".
     * Pass country name from template (e.g. from countryNames[address.country_id]).
     */
    public function getFormattedAddress(string $countryName): string
    {
        $line = trim($this->firstname . ' ' . $this->lastname) . ', ' . $this->address_1 . ', ' . $this->city;
        if ($countryName !== '') {
            $line .= ', ' . $countryName;
        }

        return $line;
    }
}
