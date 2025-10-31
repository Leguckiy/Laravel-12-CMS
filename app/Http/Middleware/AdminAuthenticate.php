<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Set user's language preference
        $user = Auth::guard('admin')->user();
        
        if ($user?->language?->code) {
            $locale = $user->language->code;
            // Check if locale directory exists, otherwise use config default
            $localePath = lang_path($locale);
            if (is_dir($localePath)) {
                App::setLocale($locale);
            } else {
                App::setLocale(config('app.locale', 'en'));
            }
        } else {
            // If user has no language, use default from config
            App::setLocale(config('app.locale', 'en'));
        }

        // Make language_id available for controllers
        $request->attributes->set('language_id', $user?->language_id);

        return $next($request);
    }
}
