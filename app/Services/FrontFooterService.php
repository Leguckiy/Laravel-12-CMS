<?php

namespace App\Services;

use App\Models\Page;
use App\Support\FrontContext;
use Illuminate\Support\Facades\Route;

class FrontFooterService
{
    public function __construct(
        protected FrontContext $context
    ) {}

    /**
     * Get footer columns (title + links) from config and DB.
     *
     * @return array<int, array{id: string, title: string, links: array<int, array{label: string, url: string}>}>
     */
    public function getColumns(): array
    {
        $columns = config('front.footer.columns', []);

        foreach ($columns as &$column) {
            if (($column['links_source'] ?? null) === 'pages') {
                $column['links'] = $this->getPageLinks();
            }
        }

        $this->translateColumns($columns);

        return $columns;
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    protected function getPageLinks(): array
    {
        $language = $this->context->getLanguage();
        if ($language === null) {
            return [];
        }

        $languageId = $language->id;
        $langCode = $language->code;

        $pages = Page::query()
            ->where('status', true)
            ->whereHas('translations', fn ($q) => $q->where('language_id', $languageId))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['translations' => fn ($q) => $q->where('language_id', $languageId)])
            ->get();

        $links = [];
        foreach ($pages as $page) {
            $translation = $page->translations->first();
            if (! $translation) {
                continue;
            }
            $links[] = [
                'label' => $translation->title,
                'url' => Route::has('front.page.show')
                    ? route('front.page.show', ['lang' => $langCode, 'slug' => $translation->slug])
                    : '#',
            ];
        }

        return $links;
    }

    /**
     * @param  array<int, array{id: string, title: string, links: array}>  $columns
     */
    protected function translateColumns(array &$columns): void
    {
        foreach ($columns as &$column) {
            if (! empty($column['title'])) {
                $column['title'] = $this->translateKey('footer.' . $column['id'] . '_title', $column['title']);
            }

            if (! empty($column['links']) && ($column['links_source'] ?? null) !== 'pages') {
                foreach ($column['links'] as $index => &$link) {
                    if (! empty($link['label'])) {
                        $link['label'] = $this->translateKey(
                            'footer.' . $column['id'] . '_' . $index,
                            $link['label']
                        );
                    }
                }
            }
        }
    }

    protected function translateKey(string $key, string $fallback): string
    {
        $translated = __('front/general.' . $key);

        return $translated !== 'front/general.' . $key ? $translated : $fallback;
    }
}
