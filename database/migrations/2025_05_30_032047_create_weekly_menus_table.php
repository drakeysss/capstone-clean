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
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->string('meal_type'); // breakfast, lunch, dinner
            $table->unsignedTinyInteger('week_cycle'); // 1 for Week 1&3, 2 for Week 2&4
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_available')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index('day_of_week');
            $table->index('meal_type');
            $table->index('week_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_menus');
    }
};
