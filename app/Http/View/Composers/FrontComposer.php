<?php

namespace App\Http\View\Composers;

use App\Http\Controllers\FrontController as BaseFrontController;
use App\Services\FrontFooterService;
use App\Services\FrontMenuService;
use App\Services\SettingService;
use App\Support\FrontContext;
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
}
