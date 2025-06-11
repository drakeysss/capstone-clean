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
        Schema::table('pre_orders', function (Blueprint $table) {
            // Rename order_date to date to match the model and controllers
            $table->renameColumn('order_date', 'date');

            // Rename student_id to user_id to match the model
            $table->renameColumn('student_id', 'user_id');

            // Add missing columns that the model expects
            $table->boolean('is_attending')->default(true)->after('meal_type');
            $table->boolean('is_prepared')->default(false)->after('is_attending');
            $table->text('notes')->nullable()->after('is_prepared');

            // Drop columns that are not used in the current model
            $table->dropColumn(['selected_items', 'special_requests', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('date', 'order_date');
            $table->renameColumn('user_id', 'student_id');

            // Remove added columns
            $table->dropColumn(['is_attending', 'is_prepared', 'notes']);

            // Add back the dropped columns
            $table->json('selected_items')->nullable();
            $table->text('special_requests')->nullable();
            $table->string('status')->default('pending');
        });
    }
};
