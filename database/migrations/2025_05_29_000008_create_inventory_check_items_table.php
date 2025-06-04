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
        Schema::create('inventory_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_check_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->decimal('current_stock', 10, 2);
            $table->boolean('needs_restock')->default(false);
            $table->decimal('item_value', 10, 2)->nullable()->comment('Value of this item at time of check');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate items in a check
            $table->unique(['inventory_check_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_check_items');
    }
};
