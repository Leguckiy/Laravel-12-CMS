<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use App\Models\CustomerGroupLang;
use App\Models\Language;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $groups = [
            ['sort_order' => 1, 'names' => ['en' => 'Default', 'uk' => 'За замовчуванням']],
            ['sort_order' => 2, 'names' => ['en' => 'Retail', 'uk' => 'Роздріб']],
            ['sort_order' => 3, 'names' => ['en' => 'Wholesale', 'uk' => 'Опт']],
        ];

        foreach ($groups as $groupData) {
            $group = CustomerGroup::create([
                'approval' => false,
                'sort_order' => $groupData['sort_order'],
            ]);

            foreach ($groupData['names'] as $languageCode => $name) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                CustomerGroupLang::create([
                    'customer_group_id' => $group->id,
                    'language_id' => $languageId,
                    'name' => $name,
                ]);
            }
        }
    }
}
