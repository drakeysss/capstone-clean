<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('ingredients');
            $table->integer('prep_time')->comment('in minutes');
            $table->integer('cooking_time')->comment('in minutes');
            $table->integer('serving_size');
            $table->string('meal_type')->comment('breakfast, lunch, dinner');
            $table->string('day_of_week');
            $table->integer('week_cycle')->comment('1 for Week 1&3, 2 for Week 2&4');
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate meals for same day/type/cycle
            $table->unique(['day_of_week', 'meal_type', 'week_cycle']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meals');
    }
}; 