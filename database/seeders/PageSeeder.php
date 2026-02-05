<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/pages.json');
        $pages = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($pages as $pageData) {
            $translations = $pageData['translations'] ?? [];
            unset($pageData['translations']);

            $page = Page::create($pageData);

            foreach ($translations as $languageCode => $translation) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                $page->translations()->create([
                    'language_id' => $languageId,
                    'title' => $translation['title'],
                    'slug' => $translation['slug'],
                    'content' => $translation['content'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                ]);
            }
        }
    }
}
