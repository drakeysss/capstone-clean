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
        // Check if the inventory table exists
        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->id();
                $table->string('item_name');
                $table->decimal('quantity', 10, 2);
                $table->string('unit');
                $table->string('category');
                $table->date('expiry_date')->nullable();
                $table->decimal('minimum_stock', 10, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the inventory table in the down method
        // because it might be used by other parts of the application
        // Schema::dropIfExists('inventory');
    }
};
