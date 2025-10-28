<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Http\View\Composers\AdminComposer;
use App\Services\AdminMenuService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdminMenuService::class, function ($app) {
            return new AdminMenuService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register admin view composer for all admin views
        View::composer('admin.*', AdminComposer::class);

        // Blade ACL directives based on route names and current admin user's group
        $registerAclDirective = function (string $directiveName, string $ability): void {
            Blade::if($directiveName, function (?string $routeName = null) use ($ability): bool {
                $routeName = $routeName ?: optional(request()->route())->getName();
                $user = Auth::guard('admin')->user();
                if (!$routeName || !$user || !$user->userGroup) {
                    return false;
                }
                return $user->userGroup->hasPermissionForRoute($routeName, $ability);
            });
        };

        $registerAclDirective('canEdit', 'edit');
        $registerAclDirective('canView', 'view');
    }
}
