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
        // Kitchen Menu Polls - Created by kitchen team
        Schema::create('kitchen_menu_polls', function (Blueprint $table) {
            $table->id();
            $table->string('meal_name');
            $table->text('ingredients')->nullable();
            $table->date('poll_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->time('deadline')->default('22:00');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['poll_date', 'meal_type']);
            $table->index('status');
            $table->index('created_by');
        });

        // Student responses to kitchen polls
        Schema::create('kitchen_poll_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('kitchen_menu_polls')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->boolean('will_eat')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate responses
            $table->unique(['poll_id', 'student_id']);
            $table->index(['poll_id', 'will_eat']);
        });

        // Real-time menu updates for kitchen
        Schema::create('daily_menu_updates', function (Blueprint $table) {
            $table->id();
            $table->date('menu_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->string('meal_name');
            $table->text('ingredients')->nullable();
            $table->enum('status', ['planned', 'preparing', 'ready', 'served'])->default('planned');
            $table->integer('estimated_portions')->default(0);
            $table->integer('actual_portions')->default(0);
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint for daily meals
            $table->unique(['menu_date', 'meal_type']);
            $table->index(['menu_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_menu_updates');
        Schema::dropIfExists('kitchen_poll_responses');
        Schema::dropIfExists('kitchen_menu_polls');
    }
};
