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
        DB::statement('ALTER TABLE `countries_lang` DROP FOREIGN KEY `countries_lang_country_id_foreign`');
        DB::statement('ALTER TABLE `countries_lang` DROP FOREIGN KEY `countries_lang_language_id_foreign`');
        
        // Drop indexes created by foreign keys (they may remain after dropping foreign keys)
        Schema::table('countries_lang', function (Blueprint $table) {
            if (Schema::hasIndex('countries_lang', 'countries_lang_country_id_foreign')) {
                $table->dropIndex('countries_lang_country_id_foreign');
            }
            if (Schema::hasIndex('countries_lang', 'countries_lang_language_id_foreign')) {
                $table->dropIndex('countries_lang_language_id_foreign');
            }
        });
        
        // Add composite primary key
        Schema::table('countries_lang', function (Blueprint $table) {
            $table->primary(['country_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite primary key
        Schema::table('countries_lang', function (Blueprint $table) {
            $table->dropPrimary(['country_id', 'language_id']);
        });
        
        // Add foreign keys back
        DB::statement('ALTER TABLE `countries_lang` ADD CONSTRAINT `countries_lang_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `countries_lang` ADD CONSTRAINT `countries_lang_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE');
    }
};
