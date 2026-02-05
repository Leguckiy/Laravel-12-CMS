<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\Language;
use App\Services\SettingService;
use App\Support\FrontContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class FrontLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->route('lang');
        // Get all enabled languages once per request
        $activeLanguages = Language::query()->where('status', true)->orderBy('id')->get();
        $languagesByCode = $activeLanguages->pluck('id', 'code')->toArray();

        if (! $lang || ! array_key_exists($lang, $languagesByCode)) {
            abort(404);
        }

        $languageId = $languagesByCode[$lang];
        /** @var Language $language */
        $language = $activeLanguages->firstWhere('id', $languageId) ?? Language::findOrFail($languageId);

        URL::defaults(['lang' => $lang]);

        App::setLocale($lang);
        // Store both locale code and language_id in session
        $request->session()->put('language_id', $languageId);

        // Initialize FrontContext with language and currency
        $context = app(FrontContext::class);
        $context->setLanguage($language);
        $context->setLanguages($activeLanguages);

        // Get currency from session or settings
        $currencyId = $request->session()->get('currency_id');
        /** @var SettingService $settingService */
        $settingService = app(SettingService::class);

        if (! $currencyId) {
            $currencyId = $settingService->get('config_currency_id');
            $request->session()->put('currency_id', $currencyId);
        }

        // Load all active currencies once per request
        $activeCurrencies = Currency::query()->where('status', true)->orderBy('id')->get();
        /** @var Currency|null $currency */
        $currency = $activeCurrencies->firstWhere('id', $currencyId) ?? $activeCurrencies->first();

        if ($currency) {
            $context->setCurrency($currency);
            $context->setCurrencies($activeCurrencies);
        }

        return $next($request);
    }
}
