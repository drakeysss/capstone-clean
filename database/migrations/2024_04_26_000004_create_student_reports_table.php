<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('student_reports')) {
            Schema::create('student_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('report_type', ['meal_consumption', 'nutrition', 'attendance']);
                $table->date('start_date');
                $table->date('end_date');
                $table->json('data');
                $table->text('summary')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_reports');
    }
}; 