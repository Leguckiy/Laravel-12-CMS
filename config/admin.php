<?php

return [
    'menu' => [
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
            'id' => 'menu-sale',
            'name' => 'Sales',
            'icon' => 'fas fa-shopping-cart',
            'route' => '',
            'children' => [
                [
                    'name' => 'Orders',
                    'route' => '',
                    'children' => []
                ]
            ]
        ],
        [
            'id' => 'menu-customer',
            'name' => 'Customers',
            'icon' => 'fas fa-user',
            'route' => '',
            'children' => [
                [
                    'name' => 'Customers',
                    'route' => '',
                    'children' => []
                ],
                [
                    'name' => 'Customer Groups',
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
                    'name' => 'Settings',
                    'route' => '',
                    'children' => []
                ],
                [
                    'name' => 'Users',
                    'route' => '',
                    'children' => [
                        [
                            'name' => 'Users',
                            'route' => 'admin.user.index',
                            'children' => []
                        ],
                        [
                            'name' => 'User groups',
                            'route' => 'admin.user_group.index',
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => 'Localisation',
                    'route' => '',
                    'children' => [
                        [
                            'name' => 'Languages',
                            'route' => 'admin.language.index',
                            'children' => []
                        ],
                        [
                            'name' => 'Currencies',
                            'route' => '',
                            'children' => []
                        ],
                        [
                            'name' => 'Stock Statuses',
                            'route' => '',
                            'children' => []
                        ],
                        [
                            'name' => 'Order Statuses',
                            'route' => '',
                            'children' => []
                        ],
                        [
                            'name' => 'Countries',
                            'route' => '',
                            'children' => []
                        ]
                    ]
                ]
            ]
        ]
    ],

    'permissions_mapping' => [
        'user' => 'user/user',
        'user_group' => 'user/user_group',
        'language' => 'localisation/language',
    ]
];
