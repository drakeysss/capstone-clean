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
        Schema::table('feedback', function (Blueprint $table) {
            // Add meal_id column if it doesn't exist
            if (!Schema::hasColumn('feedback', 'meal_id')) {
                $table->foreignId('meal_id')->nullable()->constrained('meals')->onDelete('cascade')->after('student_id');
            }

            // Add suggestions column if it doesn't exist
            if (!Schema::hasColumn('feedback', 'suggestions')) {
                $table->text('suggestions')->nullable()->after('comments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            if (Schema::hasColumn('feedback', 'meal_id')) {
                $table->dropForeign(['meal_id']);
                $table->dropColumn('meal_id');
            }

            if (Schema::hasColumn('feedback', 'suggestions')) {
                $table->dropColumn('suggestions');
            }
        });
    }
};
