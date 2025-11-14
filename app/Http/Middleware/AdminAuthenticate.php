<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Support\AdminContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function __construct(private AdminContext $context) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        if ($user) {
            $user->loadMissing('language');
        }

        $locale = $user?->language?->code ?? config('app.locale', 'en');
        $localePath = lang_path($locale);

        if (! is_dir($localePath)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        $language = $user?->language;

        if (! $language) {
            $language = Language::query()
                ->where('code', $locale)
                ->first();
        }

        $this->context
            ->setUser($user)
            ->setLanguage($language);

        $request->attributes->set('language_id', $language->id);

        return $next($request);
    }
}
