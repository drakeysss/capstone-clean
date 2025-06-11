<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('week_cycle')->default(1);
                $table->string('day');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->default('regular');
                $table->string('meal_type');
                $table->date('date')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index('meal_type');
                $table->index('date');
                $table->index('week_cycle');
                $table->index('day');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
}; 