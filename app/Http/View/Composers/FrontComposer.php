<?php

namespace App\Http\View\Composers;

use App\Http\Controllers\FrontController as BaseFrontController;
use App\Models\Currency;
use App\Models\Language;
use App\Services\FrontFooterService;
use App\Services\FrontMenuService;
use App\Services\SettingService;
use App\Support\FrontContext;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class FrontComposer
{
    public function __construct(
        protected FrontContext $context,
        protected SettingService $settingService,
        protected FrontMenuService $frontMenuService,
        protected FrontFooterService $frontFooterService,
    ) {}

    public function compose(View $view): void
    {
        if ($this->context->language === null) {
            $this->ensureContextFromSession();
        }

        $controller = request()->route()?->getController();
        $frontLanguageUrls = ($controller instanceof BaseFrontController && method_exists($controller, 'getLanguageUrls'))
            ? $controller->getLanguageUrls()
            : [];

        $view->with([
            'frontLanguage' => $this->context->language,
            'frontCurrency' => $this->context->currency,
            'frontLanguages' => $this->context->getLanguages(),
            'frontCurrencies' => $this->context->getCurrencies(),
            'frontSettings' => $this->settingService->all(),
            'frontMenuItems' => $this->frontMenuService->getMenuItems(),
            'frontFooterColumns' => $this->frontFooterService->getColumns(),
            'frontLanguageUrls' => $frontLanguageUrls,
        ]);
    }

    /**
     * Set context from session/default when no route matched (e.g. 404) so 404 page uses session language.
     */
    protected function ensureContextFromSession(): void
    {
        $languageId = session('language_id');
        if (! $languageId) {
            $languageId = $this->settingService->get('config_language_id');
        }
        $languageId = $languageId ? (int) $languageId : null;

        $languages = Language::query()->where('status', true)->orderBy('id')->get();
        $language = $languageId
            ? $languages->firstWhere('id', $languageId) ?? $languages->first()
            : $languages->first();

        if ($language) {
            $this->context->setLanguage($language);
            $this->context->setLanguages($languages);
            App::setLocale($language->code);
        }

        $currencyId = session('currency_id') ?? $this->settingService->get('config_currency_id');
        $currencyId = $currencyId ? (int) $currencyId : null;
        $currencies = Currency::query()->where('status', true)->orderBy('id')->get();
        $currency = $currencyId ? $currencies->firstWhere('id', $currencyId) ?? $currencies->first() : $currencies->first();
        if ($currency) {
            $this->context->setCurrency($currency);
            $this->context->setCurrencies($currencies);
        }
    }
}
