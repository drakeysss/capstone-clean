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
        // Check if the announcements table exists
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('expiry_date');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_poll')->default(false);
                $table->json('poll_options')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the announcements table in the down method
        // because it might be used by other parts of the application
        // Schema::dropIfExists('announcements');
    }
};
