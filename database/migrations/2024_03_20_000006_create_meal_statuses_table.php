<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meal_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained('meals')->onDelete('cascade');
            $table->date('meal_date');
            $table->string('status')->default('Not Started')->comment('Not Started, In Progress, Completed');
            $table->foreignId('updated_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate statuses
            $table->unique(['meal_id', 'meal_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_statuses');
    }
}; 