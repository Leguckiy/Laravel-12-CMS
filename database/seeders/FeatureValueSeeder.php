<?php

namespace Database\Seeders;

use App\Models\FeatureValue;
use App\Models\FeatureValueLang;
use App\Models\Language;
use Illuminate\Database\Seeder;

class FeatureValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/feature_values.json');
        $featureValues = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($featureValues as $valueData) {
            $translations = $valueData['translations'] ?? [];
            $featureId = (int) ($valueData['feature_id'] ?? 0);
            unset($valueData['translations'], $valueData['feature_id']);

            if ($featureId === 0) {
                continue;
            }

            // Create feature value
            $featureValue = FeatureValue::create([
                'feature_id' => $featureId,
                'sort_order' => $valueData['sort_order'],
            ]);

            // Create translations per language
            foreach ($translations as $languageCode => $value) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                FeatureValueLang::create([
                    'feature_value_id' => $featureValue->id,
                    'language_id' => $languageId,
                    'value' => $value,
                ]);
            }
        }
    }
}
