<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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

                $table->index('expiry_date');
                $table->index('is_active');
                $table->index('is_poll');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}; 