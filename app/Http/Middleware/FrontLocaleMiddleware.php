<?php

namespace App\Http\Middleware;

use App\Models\Language;
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
        App::setLocale($lang);
        // Store both locale code and language_id in session
        $request->session()->put('language_id', $languageId);
        return $next($request);
    }
}
