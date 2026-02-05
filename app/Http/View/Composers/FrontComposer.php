<?php

namespace App\Http\View\Composers;

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
    ) {}

    public function compose(View $view): void
    {
        $view->with([
            'frontLanguage' => $this->context->language,
            'frontCurrency' => $this->context->currency,
            'frontLanguages' => $this->context->getLanguages(),
            'frontCurrencies' => $this->context->getCurrencies(),
            'frontSettings' => $this->settingService->all(),
            'frontMenuItems' => $this->frontMenuService->getMenuItems(),
        ]);
    }
}
