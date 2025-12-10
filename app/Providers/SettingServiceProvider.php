<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SettingService;

class SettingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SettingService::class, function () {
            return new SettingService();
        });
    }

    public function boot()
    {
        //
    }
}
