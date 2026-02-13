<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot/lang table renames: plural to singular (Laravel convention).
     *
     * @var array<string, string> old name => new name
     */
    private const RENAMES = [
        'categories_lang' => 'category_lang',
        'countries_lang' => 'country_lang',
        'customer_groups_lang' => 'customer_group_lang',
        'feature_values_lang' => 'feature_value_lang',
        'features_lang' => 'feature_lang',
        'order_statuses_lang' => 'order_status_lang',
        'pages_lang' => 'page_lang',
        'products_lang' => 'product_lang',
        'settings_lang' => 'setting_lang',
        'stock_statuses_lang' => 'stock_status_lang',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::RENAMES as $from => $to) {
            if (Schema::hasTable($from) && ! Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::RENAMES as $from => $to) {
            if (Schema::hasTable($to) && ! Schema::hasTable($from)) {
                Schema::rename($to, $from);
            }
        }
    }
};
