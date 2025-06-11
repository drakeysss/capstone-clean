<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->decimal('minimum_quantity', 10, 2);
            $table->decimal('reorder_point', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            
            // Add index for faster searches
            $table->index(['name', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_items');
    }
}; 