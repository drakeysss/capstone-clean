<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meal_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained('meals')->onDelete('cascade');
            $table->date('poll_date');
            $table->string('meal_type')->comment('breakfast, lunch, dinner');
            $table->integer('votes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate polls
            $table->unique(['meal_id', 'poll_date']);
        });

        Schema::create('meal_poll_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('meal_polls')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users');
            $table->boolean('will_attend')->default(true);
            $table->text('preference_notes')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate responses
            $table->unique(['poll_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_poll_responses');
        Schema::dropIfExists('meal_polls');
    }
}; 