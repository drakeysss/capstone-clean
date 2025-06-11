<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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

                $table->index('item_name');
                $table->index('category');
                $table->index('expiry_date');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}; 