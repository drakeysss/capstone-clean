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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type'); // e.g., 'menu_update', 'user_input', etc.
            $table->string('module'); // e.g., 'menu', 'inventory', etc.
            $table->text('description'); // Description of the action
            $table->longText('old_values')->nullable(); // JSON of old values
            $table->longText('new_values')->nullable(); // JSON of new values
            $table->text('user_input')->nullable(); // Raw user input
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index('action_type');
            $table->index('module');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
