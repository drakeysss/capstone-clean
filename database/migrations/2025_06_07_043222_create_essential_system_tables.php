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
        // Create menus table for weekly menu system
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->default('regular');
                $table->string('meal_type'); // breakfast, lunch, dinner
                $table->date('date');
                $table->decimal('price', 8, 2)->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->integer('week_cycle')->default(1);
                $table->string('day')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');
            });
        }

        // Create weekly_menus table
        if (!Schema::hasTable('weekly_menus')) {
            Schema::create('weekly_menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('day_of_week'); // monday, tuesday, etc.
                $table->string('meal_type'); // breakfast, lunch, dinner
                $table->integer('week_cycle')->default(1);
                $table->decimal('price', 8, 2)->default(0);
                $table->boolean('is_available')->default(true);
                $table->unsignedBigInteger('created_by');
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
            });
        }

        // Create meals table
        if (!Schema::hasTable('meals')) {
            Schema::create('meals', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('ingredients')->nullable();
                $table->integer('prep_time')->nullable();
                $table->integer('cooking_time')->nullable();
                $table->integer('serving_size')->nullable();
                $table->string('meal_type');
                $table->string('day_of_week');
                $table->integer('week_cycle')->default(1);
                $table->timestamps();
            });
        }

        // Create meal_statuses table
        if (!Schema::hasTable('meal_statuses')) {
            Schema::create('meal_statuses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('meal_id');
                $table->string('status'); // preparing, ready, served
                $table->timestamp('status_time');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
            });
        }

        // Create meal_polls table
        if (!Schema::hasTable('meal_polls')) {
            Schema::create('meal_polls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('meal_id');
                $table->date('poll_date');
                $table->string('meal_type');
                $table->integer('votes')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
            });
        }

        // Create meal_poll_responses table
        if (!Schema::hasTable('meal_poll_responses')) {
            Schema::create('meal_poll_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('poll_id');
                $table->unsignedBigInteger('student_id');
                $table->boolean('will_attend')->default(false);
                $table->text('preference_notes')->nullable();
                $table->timestamps();

                $table->foreign('poll_id')->references('id')->on('meal_polls')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['poll_id', 'student_id']);
            });
        }

        // Create polls table for kitchen menu polling
        if (!Schema::hasTable('polls')) {
            Schema::create('polls', function (Blueprint $table) {
                $table->id();
                $table->date('poll_date');
                $table->string('meal_type');
                $table->text('instructions')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
            });
        }

        // Create poll_responses table
        if (!Schema::hasTable('poll_responses')) {
            Schema::create('poll_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('poll_id');
                $table->unsignedBigInteger('user_id');
                $table->json('selected_items')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['poll_id', 'user_id']);
            });
        }

        // Create kitchen_menu_polls table
        if (!Schema::hasTable('kitchen_menu_polls')) {
            Schema::create('kitchen_menu_polls', function (Blueprint $table) {
                $table->id();
                $table->date('poll_date');
                $table->string('meal_type');
                $table->json('menu_options');
                $table->text('instructions')->nullable();
                $table->datetime('deadline');
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('meal_id')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('meal_id')->references('id')->on('meals')->onDelete('set null');
            });
        }

        // Create pre_orders table
        if (!Schema::hasTable('pre_orders')) {
            Schema::create('pre_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('menu_id')->nullable();
                $table->date('order_date');
                $table->string('meal_type');
                $table->json('selected_items')->nullable();
                $table->text('special_requests')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('set null');
            });
        }

        // Create feedback table
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('meal_id')->nullable();
                $table->string('meal_type');
                $table->date('meal_date');
                $table->integer('rating')->default(1);
                $table->json('food_quality')->nullable();
                $table->text('comments')->nullable();
                $table->text('suggestions')->nullable();
                $table->json('dietary_concerns')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('meal_id')->references('id')->on('meals')->onDelete('set null');
            });
        }

        // Create announcements table
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->unsignedBigInteger('user_id');
                $table->date('expiry_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_poll')->default(false);
                $table->json('poll_options')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to handle foreign key constraints
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('pre_orders');
        Schema::dropIfExists('kitchen_menu_polls');
        Schema::dropIfExists('poll_responses');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('meal_poll_responses');
        Schema::dropIfExists('meal_polls');
        Schema::dropIfExists('meal_statuses');
        Schema::dropIfExists('meals');
        Schema::dropIfExists('weekly_menus');
        Schema::dropIfExists('menus');
    }
};
