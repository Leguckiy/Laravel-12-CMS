<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            ShippingMethodSeeder::class,
            CustomerGroupSeeder::class,
            StockStatusSeeder::class,
            OrderStatusSeeder::class,
            PaymentMethodSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            FeatureSeeder::class,
            FeatureValueSeeder::class,
            ProductSeeder::class,
            UserGroupSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
