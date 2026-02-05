<?php

namespace App\Services;

use App\Models\Category;
use App\Support\FrontContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class FrontMenuService
{
    public function __construct(
        protected FrontContext $context,
    ) {}

    /**
     * Get menu items (active categories with translation for current language).
     *
     * @return array<int, array{id: int, name: string, slug: string, url: string}>
     */
    public function getMenuItems(): array
    {
        $language = $this->context->getLanguage();
        if ($language === null) {
            return [];
        }

        $languageId = $language->id;

        $categories = Category::query()
            ->where('status', true)
            ->with(['translations' => fn ($q) => $q->where('language_id', $languageId)])
            ->orderBy('sort_order')
            ->get();

        return $this->mapToMenuItems($categories, $languageId);
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return array<int, array{id: int, name: string, slug: string, url: string}>
     */
    protected function mapToMenuItems(Collection $categories, int $languageId): array
    {
        $items = [];
        $langCode = $this->context->getLanguage()?->code ?? config('app.locale');

        foreach ($categories as $category) {
            $translation = $category->translation($languageId);
            if (! $translation || ! $translation->name) {
                continue;
            }

            $items[] = [
                'id' => $category->id,
                'name' => $translation->name,
                'slug' => $translation->slug,
                'url' => $this->buildCategoryUrl($langCode, $translation->slug),
            ];
        }

        return $items;
    }

    protected function buildCategoryUrl(string $langCode, string $slug): string
    {
        if (Route::has('front.category.show')) {
            return route('front.category.show', ['lang' => $langCode, 'slug' => $slug]);
        }

        return '#';
    }
}
