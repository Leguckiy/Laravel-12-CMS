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
     * Build language URLs for the switcher from translations (with language relation loaded).
     *
     * @param  Collection<int, object>  $translations
     */
    protected function setLanguageUrlsFromTranslations(Collection $translations, string $routeName): void
    {
        $this->languageUrls = $translations
            ->keyBy(fn ($t) => $t->language->code)
            ->map(fn ($t) => route($routeName, ['lang' => $t->language->code, 'slug' => $t->slug]))
            ->toArray();
    }
}
