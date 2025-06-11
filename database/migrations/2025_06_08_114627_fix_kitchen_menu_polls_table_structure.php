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
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            // Add missing columns
            $table->string('meal_name')->after('id');
            $table->text('ingredients')->nullable()->after('meal_name');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft')->after('deadline');
            $table->timestamp('sent_at')->nullable()->after('status');

            // Drop old columns that are no longer needed
            $table->dropColumn(['menu_options', 'instructions', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            // Restore old columns
            $table->longText('menu_options')->after('meal_type');
            $table->text('instructions')->nullable()->after('menu_options');
            $table->boolean('is_active')->default(true)->after('deadline');

            // Drop new columns
            $table->dropColumn(['meal_name', 'ingredients', 'status', 'sent_at']);
        });
    }
};
