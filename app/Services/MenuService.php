<?php

namespace App\Services;

class MenuService
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
        $this->menuItems = [
            [
                'id' => 'menu-dashboard',
                'name' => 'Dashboard',
                'icon' => 'fas fa-home',
                'route' => 'admin.dashboard',
                'children' => []
            ],
            [
                'id' => 'menu-catalog',
                'name' => 'Catalog',
                'icon' => 'fas fa-box',
                'route' => '',
                'children' => [
                    [
                        'name' => 'Categories',
                        'route' => '',
                        'children' => []
                    ],
                    [
                        'name' => 'Products',
                        'route' => '',
                        'children' => []
                    ]
                ]
            ],
            [
                'id' => 'menu-settings',
                'name' => 'Settings',
                'icon' => 'fas fa-cog',
                'route' => '',
                'children' => [
                    [
                        'name' => 'Users',
                        'route' => '',
                        'children' => [
                            [
                                'name' => 'User list',
                                'route' => '',
                                'children' => []
                            ],
                            [
                                'name' => 'User groups',
                                'route' => '',
                                'children' => []
                            ]
                        ]
                    ]
                ]
            ]
        ];
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
