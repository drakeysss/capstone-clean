<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->string('meal_type')->comment('breakfast, lunch, dinner');
            $table->date('meal_date');
            $table->integer('rating')->comment('1-5 rating');
            $table->json('food_quality')->comment('JSON array of selected quality aspects');
            $table->text('comments')->nullable();
            $table->json('dietary_concerns')->nullable()->comment('JSON array of dietary concerns');
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
            
            // Add index for faster queries
            $table->index(['meal_date', 'meal_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}; 