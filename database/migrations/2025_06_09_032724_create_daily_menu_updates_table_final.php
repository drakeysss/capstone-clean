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
        Schema::create('daily_menu_updates', function (Blueprint $table) {
            $table->id();
            $table->date('menu_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->string('meal_name');
            $table->text('ingredients')->nullable();
            $table->enum('status', ['planned', 'preparing', 'ready', 'served'])->default('planned');
            $table->integer('estimated_portions')->default(0);
            $table->integer('actual_portions')->default(0);
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint for daily meals
            $table->unique(['menu_date', 'meal_type']);
            $table->index(['menu_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_menu_updates');
    }
};
