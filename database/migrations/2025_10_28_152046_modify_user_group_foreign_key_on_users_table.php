<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['user_group_id']);
            
            // Modify column to be nullable
            $table->unsignedBigInteger('user_group_id')->nullable()->change();
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add new foreign key with set null on delete
            $table->foreign('user_group_id')
                ->references('id')
                ->on('user_groups')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the set null foreign key
            $table->dropForeign(['user_group_id']);
            
            // Modify column to be not nullable
            $table->unsignedBigInteger('user_group_id')->nullable(false)->change();
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add back the cascade foreign key
            $table->foreign('user_group_id')
                ->references('id')
                ->on('user_groups')
                ->onDelete('cascade');
        });
    }
};
