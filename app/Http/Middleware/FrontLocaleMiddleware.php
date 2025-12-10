<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\Language;
use App\Services\SettingService;
use App\Support\FrontContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        // Get all enabled language codes
        $activeLanguages = Language::query()->where('status', true)->pluck('id', 'code')->toArray();

        if (! $lang || ! array_key_exists($lang, $activeLanguages)) {
            abort(404);
        }

        $languageId = $activeLanguages[$lang];
        $language = Language::findOrFail($languageId);

        App::setLocale($lang);
        // Store both locale code and language_id in session
        $request->session()->put('language_id', $languageId);

        // Initialize FrontContext with language and currency
        $context = app(FrontContext::class);
        $context->setLanguage($language);

        // Get currency from session or settings
        $currencyId = $request->session()->get('currency_id');
        if (! $currencyId) {
            /** @var SettingService $settingService */
            $settingService = app(SettingService::class);
            $currencyId = $settingService->get('config_currency_id');
            $request->session()->put('currency_id', $currencyId);
                }

        $currency = Currency::find($currencyId) ?? Currency::where('status', true)->orderBy('id')->first();

        if ($currency) {
            $context->setCurrency($currency);
        }

        return $next($request);
    }
}
