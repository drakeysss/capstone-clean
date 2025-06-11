<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users');
            $table->string('report_type')->comment('inventory, equipment, safety, other');
            $table->string('status')->default('pending')->comment('pending, in_progress, resolved');
            $table->string('priority')->default('medium')->comment('low, medium, high, urgent');
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            // Add index for faster queries
            $table->index(['status', 'priority']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_reports');
    }
}; 