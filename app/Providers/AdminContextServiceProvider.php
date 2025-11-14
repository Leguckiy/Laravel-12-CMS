<?php

namespace App\Providers;

use App\Support\AdminContext;
use Illuminate\Support\ServiceProvider;

class AdminContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(AdminContext::class, fn (): AdminContext => new AdminContext);
    }

    public function boot(): void
    {
        //
    }
}
