<?php

namespace App\Observers;

use App\Models\Address;

class AddressObserver
{
    /**
     * When an address is set as default, clear default on all other addresses of the same customer.
     */
    public function saving(Address $address): void
    {
        if (! $address->default) {
            return;
        }

        $query = Address::query()
            ->where('customer_id', $address->customer_id);

        if ($address->exists) {
            $query->whereKeyNot($address->getKey());
        }

        $query->update(['default' => false]);
    }
}
