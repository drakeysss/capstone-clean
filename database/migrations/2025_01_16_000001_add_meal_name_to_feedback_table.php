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
            // Add meal_name column for manual meal input
            $table->string('meal_name')->nullable()->after('meal_id');

            // Drop foreign key constraint first
            $table->dropForeign(['meal_id']);

            // Make meal_id nullable since we're allowing manual input
            $table->unsignedBigInteger('meal_id')->nullable()->change();

            // Re-add foreign key constraint with nullable
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Remove meal_name column
            $table->dropColumn('meal_name');

            // Drop foreign key constraint
            $table->dropForeign(['meal_id']);

            // Make meal_id required again
            $table->unsignedBigInteger('meal_id')->nullable(false)->change();

            // Re-add foreign key constraint as required
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
        });
    }
};
