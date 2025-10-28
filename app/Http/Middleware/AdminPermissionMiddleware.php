<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Automatically checks permissions based on route name
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $ability  The permission ability to check: 'view' or 'edit'
     */
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $routeName = $request->route()->getName();
        
        if (!$routeName) {
            return $next($request);
        }

        $user = Auth::guard('admin')->user();
        
        if (!$user || !$user->userGroup) {
            return abort(403);
        }

        // Detect ability from route action if not provided
        if (!$ability) {
            $ability = $this->detectAbility($routeName);
        }

        // Check if user has permission for this route
        if (!$user->userGroup->hasPermissionForRoute($routeName, $ability)) {
            return abort(403);
        }

        return $next($request);
    }

    /**
     * Detect ability type from route name
     * 
     * @param string $routeName
     * @return string 'view' or 'edit'
     */
    protected function detectAbility(string $routeName): string
    {
        // Check if route is a view-only action
        if (str_ends_with($routeName, '.index') || str_ends_with($routeName, '.show')) {
            return 'view';
        }
        
        // All other actions require edit permission
        return 'edit';
    }
}

