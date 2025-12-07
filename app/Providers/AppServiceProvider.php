<?php

namespace App\Providers;

use App\Http\View\Composers\AdminComposer;
use App\Services\AdminMenuService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdminMenuService::class, function ($app) {
            return new AdminMenuService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination views
        Paginator::useBootstrapFive();

        // Register admin view composer for all admin views except login
        View::composer('admin.*', function (ViewInstance $view): void {
            if ($view->name() === 'admin.login') {
                return;
            }

            app(AdminComposer::class)->compose($view);
        });

        // Blade ACL directives based on route names and current admin user's group
        $registerAclDirective = function (string $directiveName, string $ability): void {
            Blade::if($directiveName, function (?string $routeName = null) use ($ability): bool {
                $routeName = $routeName ?: optional(request()->route())->getName();
                $user = Auth::guard('admin')->user();
                if (! $routeName || ! $user || ! $user->userGroup) {
                    return false;
                }

                return $user->userGroup->hasPermissionForRoute($routeName, $ability);
            });
        };

        $registerAclDirective('canEdit', 'edit');
        $registerAclDirective('canView', 'view');
    }
}
