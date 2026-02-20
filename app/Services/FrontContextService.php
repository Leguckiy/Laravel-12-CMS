<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Currency;
use App\Models\Language;
use App\Support\FrontContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FrontContextService
{
    public function __construct(
        protected FrontContext $context,
        protected SettingService $settingService,
    ) {}

    /**
     * Initialize front context from session or config (language, currency).
     * Used when no route matched (404) or before overriding language from URL in middleware.
     */
    public function initializeFromSession(Request $request): void
    {
        $hasSession = $request->hasSession();

        $languageId = $hasSession ? $request->session()->get('language_id') : null;
        if (! $languageId) {
            $languageId = $this->settingService->get('config_language_id');
            if ($hasSession) {
                $request->session()->put('language_id', $languageId);
            }
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

        $currencyId = $hasSession ? $request->session()->get('currency_id') : null;
        if (! $currencyId) {
            $currencyId = $this->settingService->get('config_currency_id');
            if ($hasSession) {
                $request->session()->put('currency_id', $currencyId);
            }
        }
        $currencyId = $currencyId ? (int) $currencyId : null;
        $currencies = Currency::query()->where('status', true)->orderBy('id')->get();
        $currency = $currencyId
            ? $currencies->firstWhere('id', $currencyId) ?? $currencies->first()
            : $currencies->first();

        if ($currency) {
            $this->context->setCurrency($currency);
            $this->context->setCurrencies($currencies);
        }

        $cart = null;
        if ($hasSession && $request->session()->get('cart_token') === null) {
            $request->session()->put('cart_token', Str::random(40));
        }
        if (Auth::guard('web')->check()) {
            $cart = Cart::findForCustomer((int) Auth::guard('web')->id());
        }
        if ($cart === null && $hasSession) {
            $cart = Cart::findByToken($request->session()->get('cart_token'));
        }
        $this->context->setCart($cart);

        $this->context->setCustomer(Auth::guard('web')->user());
    }
}
