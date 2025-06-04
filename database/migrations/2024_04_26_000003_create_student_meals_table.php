<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('student_meals')) {
            Schema::create('student_meals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
                $table->string('menu_item');
                $table->integer('calories')->nullable();
                $table->decimal('protein', 5, 2)->nullable();
                $table->decimal('carbs', 5, 2)->nullable();
                $table->decimal('fats', 5, 2)->nullable();
                $table->timestamp('consumed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_meals');
    }
}; 