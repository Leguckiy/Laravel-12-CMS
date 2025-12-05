<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\StockStatus;
use App\Models\StockStatusLang;
use Illuminate\Database\Seeder;

class StockStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/stock_statuses.json');
        $statuses = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($statuses as $translations) {
            // Create new stock status
            $stockStatus = new StockStatus;
            $stockStatus->save();
            $stockStatusId = $stockStatus->id;

            // Create translations per language
            foreach ($translations as $languageCode => $name) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                StockStatusLang::create([
                    'stock_status_id' => $stockStatusId,
                    'language_id' => $languageId,
                    'name' => $name,
                ]);
            }
        }
    }
}
