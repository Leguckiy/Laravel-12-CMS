<?php

namespace App\Http\Middleware;

use App\Services\FrontContextService;
use App\Support\FrontContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class FrontLocaleMiddleware
{
    public function __construct(
        protected FrontContextService $frontContextService,
        protected FrontContext $context,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->frontContextService->initializeFromSession($request);

        $lang = $request->route('lang');
        $activeLanguages = $this->context->getLanguages();
        if ($activeLanguages->isEmpty()) {
            abort(404);
        }
        $languagesByCode = $activeLanguages->pluck('id', 'code')->toArray();

        if (! $lang || ! array_key_exists($lang, $languagesByCode)) {
            abort(404);
        }

        $languageId = $languagesByCode[$lang];
        $language = $activeLanguages->firstWhere('id', $languageId);

        URL::defaults(['lang' => $lang]);
        App::setLocale($lang);
        $request->session()->put('language_id', $languageId);

        $this->context->setLanguage($language);
        $this->context->setLanguages($activeLanguages);

        return $next($request);
    }
}
