<?php

return [
    'menu' => [
        [
            'id' => 'menu-dashboard',
            'name' => 'menu_dashboard',
            'icon' => 'fas fa-home',
            'route' => 'admin.dashboard',
            'children' => []
        ],
        [
            'id' => 'menu-catalog',
            'name' => 'menu_catalog',
            'icon' => 'fas fa-box',
            'route' => '',
            'children' => [
                [
                    'name' => 'menu_catalog_categories',
                    'route' => 'admin.category.index',
                    'children' => []
                ],
                [
                    'name' => 'menu_catalog_products',
                    'route' => 'admin.product.index',
                    'children' => []
                ],
                [
                    'name' => 'menu_catalog_features',
                    'route' => 'admin.feature.index',
                    'children' => []
                ]
            ]
        ],
        [
            'id' => 'menu-sale',
            'name' => 'menu_sales',
            'icon' => 'fas fa-shopping-cart',
            'route' => '',
            'children' => [
                [
                    'name' => 'menu_sales_orders',
                    'route' => '',
                    'children' => []
                ]
            ]
        ],
        [
            'id' => 'menu-customer',
            'name' => 'menu_customers',
            'icon' => 'fas fa-user',
            'route' => '',
            'children' => [
                [
                    'name' => 'menu_customers_list',
                    'route' => '',
                    'children' => []
                ],
                [
                    'name' => 'menu_customers_groups',
                    'route' => '',
                    'children' => []
                ]
            ]
        ],
        [
            'id' => 'menu-settings',
            'name' => 'menu_settings',
            'icon' => 'fas fa-cog',
            'route' => '',
            'children' => [
                [
                    'name' => 'menu_settings_general',
                    'route' => '',
                    'children' => []
                ],
                [
                    'name' => 'menu_settings_users',
                    'route' => '',
                    'children' => [
                        [
                            'name' => 'users',
                            'route' => 'admin.user.index',
                            'children' => []
                        ],
                        [
                            'name' => 'user_groups',
                            'route' => 'admin.user_group.index',
                            'children' => []
                        ]
                    ]
                ],
                [
                    'name' => 'menu_settings_localisation',
                    'route' => '',
                    'children' => [
                        [
                            'name' => 'languages',
                            'route' => 'admin.language.index',
                            'children' => []
                        ],
                        [
                            'name' => 'menu_settings_localisation_currencies',
                            'route' => 'admin.currency.index',
                            'children' => []
                        ],
                        [
                            'name' => 'menu_settings_localisation_stock_statuses',
                            'route' => 'admin.stock_status.index',
                            'children' => []
                        ],
                        [
                            'name' => 'menu_settings_localisation_order_statuses',
                            'route' => 'admin.order_status.index',
                            'children' => []
                        ],
                        [
                            'name' => 'menu_settings_localisation_countries',
                            'route' => 'admin.country.index',
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
        'currency' => 'localisation/currency',
        'stock_status' => 'localisation/stock_status',
        'order_status' => 'localisation/order_status',
        'country' => 'localisation/country',
        'category' => 'catalog/category',
        'product' => 'catalog/product',
        'feature' => 'catalog/features',
        'feature_value' => 'catalog/feature_values',
    ]
];
