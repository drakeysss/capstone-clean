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
        Schema::create('post_assessments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('prepared_quantity', 8, 2)->comment('Quantity prepared in servings');
            $table->decimal('leftover_quantity', 8, 2)->comment('Quantity leftover in servings');
            $table->decimal('wastage_percentage', 5, 2)->comment('Wastage percentage');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add unique constraint to prevent duplicate assessments
            $table->unique(['date', 'meal_type', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_assessments');
    }
};
