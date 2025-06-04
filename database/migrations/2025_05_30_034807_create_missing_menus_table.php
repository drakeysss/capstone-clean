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
        // Check if the menus table exists
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description');
                $table->string('category');
                $table->string('meal_type');
                $table->date('date');
                $table->decimal('price', 10, 2);
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Add week_cycle column that was added in later migrations
                $table->unsignedTinyInteger('week_cycle')->default(1);
                
                // Add indexes
                $table->index('meal_type');
                $table->index('date');
                $table->index('week_cycle');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the menus table in the down method
        // because it might be used by other parts of the application
        // Schema::dropIfExists('menus');
    }
};
