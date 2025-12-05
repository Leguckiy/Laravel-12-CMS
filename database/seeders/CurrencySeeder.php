<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'title' => 'Euro',
                'code' => 'EUR',
                'symbol_left' => null,
                'symbol_right' => '€',
                'decimal_place' => 2,
                'value' => 0.021,
                'status' => true,
            ],
            [
                'title' => 'US Dollar',
                'code' => 'USD',
                'symbol_left' => '$',
                'symbol_right' => null,
                'decimal_place' => 2,
                'value' => 0.024,
                'status' => true,
            ],
            [
                'title' => 'Ukrainian Hryvnia',
                'code' => 'UAH',
                'symbol_left' => null,
                'symbol_right' => '₴',
                'decimal_place' => 2,
                'value' => 1.0,
                'status' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create([
                'title' => $currency['title'],
                'code' => $currency['code'],
                'symbol_left' => $currency['symbol_left'],
                'symbol_right' => $currency['symbol_right'],
                'decimal_place' => $currency['decimal_place'],
                'value' => $currency['value'],
                'status' => $currency['status'],
            ]);
        }
    }
}
