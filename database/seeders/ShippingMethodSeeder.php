<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryIds = Country::query()
            ->where('status', true)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if ($countryIds === []) {
            return;
        }

        $methods = [
            [
                'code' => 'flat_rate',
                'config' => ['cost' => 100],
                'countries' => $countryIds,
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'code' => 'free',
                'config' => ['sub_total' => 5000],
                'countries' => $countryIds,
                'sort_order' => 2,
                'status' => true,
            ],
        ];

        foreach ($methods as $data) {
            ShippingMethod::create($data);
        }
    }
}
