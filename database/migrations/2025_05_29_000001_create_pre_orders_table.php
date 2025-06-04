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
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->boolean('is_attending')->default(true);
            $table->boolean('is_prepared')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add unique constraint to prevent duplicate pre-orders
            $table->unique(['user_id', 'date', 'meal_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
    }
};
