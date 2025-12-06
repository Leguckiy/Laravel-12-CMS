<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = \App\Models\Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/categories.json');
        $categories = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($categories as $categoryData) {
            $translations = $categoryData['translations'] ?? [];
            unset($categoryData['translations']);

            // Use image from JSON if exists, otherwise null
            if (! isset($categoryData['image'])) {
                $categoryData['image'] = null;
            }

            // Create category
            $category = Category::create($categoryData);

            // Create translations
            foreach ($translations as $languageCode => $translation) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                $category->translations()->create([
                    'language_id' => $languageId,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                ]);
            }
        }
    }
}
