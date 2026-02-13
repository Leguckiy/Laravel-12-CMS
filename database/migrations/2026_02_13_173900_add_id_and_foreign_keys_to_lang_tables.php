<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lang tables: table => [parent_id_column, parent_table].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const LANG_TABLES = [
        'category_lang' => ['category_id', 'categories'],
        'country_lang' => ['country_id', 'countries'],
        'customer_group_lang' => ['customer_group_id', 'customer_groups'],
        'feature_lang' => ['feature_id', 'features'],
        'feature_value_lang' => ['feature_value_id', 'feature_values'],
        'order_status_lang' => ['order_status_id', 'order_statuses'],
        'page_lang' => ['page_id', 'pages'],
        'product_lang' => ['product_id', 'products'],
        'setting_lang' => ['setting_id', 'settings'],
        'stock_status_lang' => ['stock_status_id', 'stock_statuses'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::LANG_TABLES as $tableName => [$parentColumn, $parentTable]) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($parentColumn): void {
                $table->dropPrimary([$parentColumn, 'language_id']);
            });

            Schema::table($tableName, function (Blueprint $table) use ($parentColumn, $parentTable): void {
                $table->id()->first();
                $table->unique([$parentColumn, 'language_id']);
                $table->foreign($parentColumn)->references('id')->on($parentTable)->cascadeOnDelete();
                $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::LANG_TABLES as $tableName => [$parentColumn, $parentTable]) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($parentColumn): void {
                $table->dropForeign([$parentColumn]);
                $table->dropForeign(['language_id']);
                $table->dropUnique([$parentColumn, 'language_id']);
                $table->dropPrimary(['id']);
            });

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('id');
            });

            Schema::table($tableName, function (Blueprint $table) use ($parentColumn): void {
                $table->primary([$parentColumn, 'language_id']);
            });
        }
    }
};
