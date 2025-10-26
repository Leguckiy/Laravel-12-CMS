<?php

namespace App\Services;

class AdminMenuService
{
    protected $menuItems = [];
    protected $currentRoute;

    public function __construct()
    {
        $this->currentRoute = request()->route()?->getName();
        $this->initializeMenu();
        $this->setActiveStates($this->menuItems);
    }

    protected function initializeMenu(): void
    {
        $this->menuItems = config('admin.menu', []);
    }

    protected function setActiveStates(array &$items): bool
    {
        $hasActive = false;

        foreach ($items as &$item) {
            $item['is_active'] = false;

            if ($this->isItemActive($item)) {
                $item['is_active'] = true;
                $hasActive = true;
            }

            if (!empty($item['children'])) {
                if ($this->setActiveStates($item['children'])) {
                    $item['is_active'] = true;
                    $hasActive = true;
                }
            }
        }

        return $hasActive;
    }

    protected function isItemActive(array $item): bool
    {
        if (empty($item['route']) || !$this->currentRoute) {
            return false;
        }

        return $item['route'] === $this->currentRoute;
    }

    public function getMenuItems(): array
    {
        return $this->menuItems;
    }
}
