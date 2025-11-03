<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign keys
        DB::statement('ALTER TABLE `stock_statuses_lang` DROP FOREIGN KEY `stock_statuses_lang_stock_status_id_foreign`');
        DB::statement('ALTER TABLE `stock_statuses_lang` DROP FOREIGN KEY `stock_statuses_lang_language_id_foreign`');
        
        // Drop indexes created by foreign keys (they may remain after dropping foreign keys)
        Schema::table('stock_statuses_lang', function (Blueprint $table) {
            if (Schema::hasIndex('stock_statuses_lang', 'stock_statuses_lang_stock_status_id_foreign')) {
                $table->dropIndex('stock_statuses_lang_stock_status_id_foreign');
            }
            if (Schema::hasIndex('stock_statuses_lang', 'stock_statuses_lang_language_id_foreign')) {
                $table->dropIndex('stock_statuses_lang_language_id_foreign');
            }
        });
        
        // Add composite primary key
        Schema::table('stock_statuses_lang', function (Blueprint $table) {
            $table->primary(['stock_status_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite primary key
        Schema::table('stock_statuses_lang', function (Blueprint $table) {
            $table->dropPrimary(['stock_status_id', 'language_id']);
        });
        
        // Add foreign keys back
        DB::statement('ALTER TABLE `stock_statuses_lang` ADD CONSTRAINT `stock_statuses_lang_stock_status_id_foreign` FOREIGN KEY (`stock_status_id`) REFERENCES `stock_statuses` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `stock_statuses_lang` ADD CONSTRAINT `stock_statuses_lang_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE');
    }
};
