<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\OrderStatus;
use App\Models\OrderStatusLang;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/order_statuses.json');
        $statuses = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($statuses as $translations) {
            // Create new order status
            $orderStatus = new OrderStatus;
            $orderStatus->save();
            $orderStatusId = $orderStatus->id;

            // Create translations per language
            foreach ($translations as $languageCode => $name) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                OrderStatusLang::create([
                    'order_status_id' => $orderStatusId,
                    'language_id' => $languageId,
                    'name' => $name,
                ]);
            }
        }
    }
}
