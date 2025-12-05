<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'code' => 'en',
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'name' => 'Українська',
                'code' => 'uk',
                'sort_order' => 2,
                'status' => true,
            ],
        ];

        foreach ($languages as $language) {
            Language::create([
                'name' => $language['name'],
                'code' => $language['code'],
                'sort_order' => $language['sort_order'],
                'status' => $language['status'],
            ]);
        }
    }
}
