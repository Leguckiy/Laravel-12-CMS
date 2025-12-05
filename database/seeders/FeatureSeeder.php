<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureLang;
use App\Models\Language;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $englishLanguageId = $languageIds['en'] ?? null;

        if ($englishLanguageId === null) {
            return;
        }

        $path = database_path('data/features.json');
        $features = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($features as $featureData) {
            $translations = $featureData['translations'] ?? [];
            $englishName = $translations['en'] ?? null;

            if ($englishName === null) {
                continue;
            }

            // Check if feature already exists
            $existingFeature = FeatureLang::query()
                ->where('language_id', $englishLanguageId)
                ->where('name', $englishName)
                ->exists();

            if ($existingFeature) {
                continue;
            }

            unset($featureData['translations']);

            // Create feature
            $feature = Feature::create($featureData);

            // Create translations per language
            foreach ($translations as $languageCode => $name) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                FeatureLang::create([
                    'feature_id' => $feature->id,
                    'language_id' => $languageId,
                    'name' => $name,
                ]);
            }
        }
    }
}
