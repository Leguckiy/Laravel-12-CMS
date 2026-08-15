<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerSearchService
{
    /**
     * Search customers by full name or email and return with their addresses.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $languageId, int $limit = 10): array
    {
        $q = trim($query);

        if ($q === '') {
            return [];
        }

        $builder = Customer::query()
            ->where('status', true)
            ->with(['addresses.country'])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->limit($limit);

        $builder->where(function ($customerQuery) use ($q): void {
            $like = '%' . Str::lower($q) . '%';

            $customerQuery
                ->whereRaw('LOWER(CONCAT(firstname, \' \', lastname)) LIKE ?', [$like])
                ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
        });

        $customers = $builder->get();

        return $customers->map(function (Customer $customer) use ($languageId): array {
            return [
                'id' => $customer->id,
                'name' => $customer->fullname,
                'email' => $customer->email,
                'addresses' => $customer->addresses
                    ->sortByDesc('default')
                    ->sortBy('id')
                    ->map(function ($address) use ($languageId): array {
                        $countryName = $address->country?->getName($languageId) ?? '';

                        return [
                            'id' => $address->id,
                            'label' => $address->getFormattedAddress($countryName),
                            'default' => (bool) $address->default,
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })->values()->all();
    }
}
