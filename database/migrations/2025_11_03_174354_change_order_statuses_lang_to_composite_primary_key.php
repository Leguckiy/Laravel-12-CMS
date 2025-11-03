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
        DB::statement('ALTER TABLE `order_statuses_lang` DROP FOREIGN KEY `order_statuses_lang_order_status_id_foreign`');
        DB::statement('ALTER TABLE `order_statuses_lang` DROP FOREIGN KEY `order_statuses_lang_language_id_foreign`');
        
        // Drop indexes created by foreign keys (they may remain after dropping foreign keys)
        Schema::table('order_statuses_lang', function (Blueprint $table) {
            if (Schema::hasIndex('order_statuses_lang', 'order_statuses_lang_order_status_id_foreign')) {
                $table->dropIndex('order_statuses_lang_order_status_id_foreign');
            }
            if (Schema::hasIndex('order_statuses_lang', 'order_statuses_lang_language_id_foreign')) {
                $table->dropIndex('order_statuses_lang_language_id_foreign');
            }
        });
        
        // Add composite primary key
        Schema::table('order_statuses_lang', function (Blueprint $table) {
            $table->primary(['order_status_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite primary key
        Schema::table('order_statuses_lang', function (Blueprint $table) {
            $table->dropPrimary(['order_status_id', 'language_id']);
        });
        
        // Add foreign keys back
        DB::statement('ALTER TABLE `order_statuses_lang` ADD CONSTRAINT `order_statuses_lang_order_status_id_foreign` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `order_statuses_lang` ADD CONSTRAINT `order_statuses_lang_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE');
    }
};
