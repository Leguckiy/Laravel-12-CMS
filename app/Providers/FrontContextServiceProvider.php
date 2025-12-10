<?php

namespace App\Providers;

use App\Support\FrontContext;
use Illuminate\Support\ServiceProvider;

class FrontContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(FrontContext::class, fn (): FrontContext => new FrontContext);
    }

    public function boot(): void
    {
        //
    }
}
