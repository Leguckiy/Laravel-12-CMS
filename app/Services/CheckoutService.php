<?php

namespace App\Services;

use App\Models\Customer;

/**
 * Helper service for checkout-related data transformations.
 */
class CheckoutService
{
    /**
     * Build registered customer session array from Customer model.
     *
     * @return array<string, mixed>
     */
    public function customerSessionFromCustomer(Customer $customer): array
    {
        return [
            'account_type' => 'register',
            'customer_id' => $customer->id,
            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            'email' => $customer->email,
        ];
    }

    /**
     * Build guest customer session array from validated data.
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    public function guestCustomerSessionFromValidated(array $validated): array
    {
        return [
            'account_type' => 'guest',
            'customer_id' => 0,
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
        ];
    }

    /**
     * Build guest shipping_address session array from validated data.
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    public function guestShippingAddressSessionFromValidated(array $validated): array
    {
        return [
            'address_id' => 0,
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'company' => $validated['company'],
            'address_1' => $validated['address_1'],
            'address_2' => $validated['address_2'],
            'city' => $validated['city'],
            'postcode' => $validated['postcode'],
            'country_id' => (int) ($validated['country_id']),
        ];
    }
}

