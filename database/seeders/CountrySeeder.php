<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/countries.json');
        $countries = json_decode((string) file_get_contents($path), true) ?? [];

        $countriesInsert = [];

        foreach ($countries as $countryData) {
            $countriesInsert[] = [
                'iso_code_2' => $countryData['iso_code_2'],
                'iso_code_3' => $countryData['iso_code_3'],
                'postcode_required' => (bool) $countryData['postcode_required'],
                'status' => (bool) $countryData['status'],
            ];
        }

        DB::table('countries')->insert($countriesInsert);

        $countryIds = DB::table('countries')
            ->pluck('id', 'iso_code_2');

        $translationsInsert = [];

        foreach ($countries as $countryData) {
            $countryId = $countryIds[$countryData['iso_code_2']] ?? null;

            if ($countryId === null) {
                continue;
            }

            foreach ($countryData['names'] as $languageCode => $name) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                $translationsInsert[] = [
                    'country_id' => $countryId,
                    'language_id' => $languageId,
                    'name' => $name,
                ];
            }
        }

        DB::table('countries_lang')->insert($translationsInsert);
    }
}
