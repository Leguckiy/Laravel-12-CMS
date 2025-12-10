<?php

namespace App\Observers;

use App\Services\SettingService;

class SettingObserver
{
    public function __construct(private SettingService $settings) {}

    public function saved(): void
    {
        $this->settings->clearCache();
    }

    public function deleted(): void
    {
        $this->settings->clearCache();
    }
}
