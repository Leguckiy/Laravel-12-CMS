<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AdminMenuService
{
    protected $menuItems = [];
    protected $currentRoute;

    public function __construct()
    {
        $this->currentRoute = request()->route()?->getName();
        $this->initializeMenu();
        $this->filterByPermissions($this->menuItems);
        $this->setActiveStates($this->menuItems);
    }

    protected function initializeMenu(): void
    {
        $this->menuItems = config('admin.menu', []);
        $this->translateMenuItems($this->menuItems);
    }

    /**
     * Recursively translate menu item names.
     */
    protected function translateMenuItems(array &$items): void
    {
        foreach ($items as &$item) {
            if (!empty($item['name'])) {
                $translationKey = "admin.{$item['name']}";
                $translated = __($translationKey);
                // If translation not found (returns same key), use formatted key as fallback
                if ($translated === $translationKey) {
                    // Format key: menu_dashboard -> Menu Dashboard
                    $item['name'] = ucwords(str_replace(['_', '-'], ' ', $item['name']));
                } else {
                    $item['name'] = $translated;
                }
            }
            
            if (!empty($item['children'])) {
                $this->translateMenuItems($item['children']);
            }
        }
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

    /**
     * Remove menu items the current admin user cannot view.
     * A parent is kept only if it has own viewable route or at least one viewable child.
     */
    protected function filterByPermissions(array &$items): bool
    {
        $user = Auth::guard('admin')->user();
        $hasVisible = false;
        $permissionsMapping = config('admin.permissions_mapping', []);

        foreach ($items as $index => &$item) {
            $childrenVisible = false;
            $hadChildren = !empty($item['children']);
            if ($hadChildren) {
                $childrenVisible = $this->filterByPermissions($item['children']);
            }

            // If item HAD children but after filtering there are none, hide the item
            if ($hadChildren && empty($item['children'])) {
                unset($items[$index]);
                continue;
            }

            // Default: show items that are not in permissions_mapping
            $selfVisible = true;
            
            if (!empty($item['route']) && $user && $user->userGroup) {
                $routeName = $item['route'];
                $parts = explode('.', $routeName);
                
                // Only check permissions for routes that are in permissions_mapping
                if (count($parts) >= 3 && $parts[0] === 'admin' && isset($permissionsMapping[$parts[1]])) {
                    // This route IS in permissions_mapping - check permissions
                    $selfVisible = $user->userGroup->hasPermissionForRoute($routeName, 'view');
                }
                // If route is NOT in permissions_mapping, keep $selfVisible = true (always show)
            }

            // If item has no route and no visible children, hide it
            if (empty($item['route']) && empty($item['children'])) {
                // No route and no children - show by default
                $hasVisible = true;
            } elseif (empty($item['route']) && !empty($item['children'])) {
                // No route but has children - keep only if children are visible
                if (!$childrenVisible) {
                    unset($items[$index]);
                    continue;
                }
                $hasVisible = true;
            } elseif (!empty($item['route'])) {
                // Has route - check if it's viewable
                if ($selfVisible || $childrenVisible) {
                    $hasVisible = true;
                } else {
                    unset($items[$index]);
                }
            }
        }

        // Reindex
        $items = array_values($items);

        return $hasVisible;
    }
}
