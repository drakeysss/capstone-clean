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
        Schema::create('inventory_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('check_date');
            $table->text('notes')->nullable();
            $table->decimal('total_value', 10, 2)->nullable()->comment('Total value of inventory at time of check');
            $table->decimal('budget_remaining', 10, 2)->nullable()->comment('Budget remaining after check');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_checks');
    }
};
