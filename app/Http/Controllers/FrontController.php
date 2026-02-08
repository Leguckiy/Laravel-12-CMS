<?php

namespace App\Http\Controllers;

use App\Support\FrontContext;
use Illuminate\Support\Collection;
use Illuminate\Routing\Controller as BaseController;

abstract class FrontController extends BaseController
{
    protected FrontContext $context;

    /**
     * URL per language code for the current page (e.g. ['en' => 'https://.../en/privacy', 'uk' => '...']).
     * Override in child controllers that need language-specific URLs for the language switcher.
     *
     * @var array<string, string>
     */
    protected array $languageUrls = [];

    public function __construct(FrontContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return array<string, string> langCode => url
     */
    public function getLanguageUrls(): array
    {
        return $this->languageUrls;
    }

    /**
     * Build language URLs for the switcher from translations.
     * Uses $languages from context to avoid redundant DB query for language relation.
     *
     * @param  Collection<int, object>  $translations  Must have language_id
     * @param  Collection<int, \App\Models\Language>  $languages
     */
    protected function setLanguageUrlsFromTranslations(Collection $translations, Collection $languages, string $routeName): void
    {
        $languagesById = $languages->keyBy('id');
        $this->languageUrls = $translations
            ->filter(fn ($t) => $languagesById->has($t->language_id))
            ->keyBy(fn ($t) => $languagesById[$t->language_id]->code)
            ->map(fn ($t) => route($routeName, ['lang' => $languagesById[$t->language_id]->code, 'slug' => $t->slug]))
            ->toArray();
    }
}
