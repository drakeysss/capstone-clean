<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->comment('Rating from 1 to 5');
            $table->text('comment')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();

            // Add unique constraint to prevent duplicate feedback
            $table->unique(['user_id', 'menu_id']);
            
            // Add index for faster queries
            $table->index(['created_at', 'rating']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_feedback');
    }
}; 