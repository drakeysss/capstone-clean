<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Add menu_id if it doesn't exist
            if (!Schema::hasColumn('feedback', 'menu_id')) {
                $table->foreignId('menu_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add suggestions if it doesn't exist
            if (!Schema::hasColumn('feedback', 'suggestions')) {
                $table->text('suggestions')->nullable();
            }
            
            // Add unique constraint if it doesn't exist
            if (!Schema::hasColumn('feedback', 'unique_user_menu')) {
                $table->unique(['user_id', 'menu_id'], 'unique_user_menu');
            }
        });
    }

    public function down()
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique('unique_user_menu');
            
            // Remove columns
            $table->dropColumn(['menu_id', 'suggestions']);
        });
    }
}; 