<?php

namespace Database\Seeders;

use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsAll = config('admin.permissions_mapping', []);

        $adminPermissions = [
            'view' => array_values($permissionsAll),
            'edit' => array_values($permissionsAll),
        ];

        UserGroup::create([
            'name' => 'Super Admin',
            'permission' => $adminPermissions,
        ]);

        UserGroup::create([
            'name' => 'Manager',
            'permission' => [
                'view' => array_values($permissionsAll),
                'edit' => [
                    'catalog/category',
                    'catalog/product',
                    'catalog/features',
                    'catalog/feature_value',
                    'customer/customer',
                    'customer/customer_group',
                    'setting/setting',
                ],
            ],
        ]);
    }
}
