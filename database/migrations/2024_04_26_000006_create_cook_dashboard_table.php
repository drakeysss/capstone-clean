<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cook_dashboard')) {
            Schema::create('cook_dashboard', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('task_description');
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
                $table->date('due_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cook_dashboard');
    }
}; 