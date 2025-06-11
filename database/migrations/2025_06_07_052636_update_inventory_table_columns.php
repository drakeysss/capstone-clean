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
        Schema::table('inventory', function (Blueprint $table) {
            // Rename item_name to name to match the model
            $table->renameColumn('item_name', 'name');

            // Rename minimum_stock to reorder_point to match the model
            $table->renameColumn('minimum_stock', 'reorder_point');

            // Add missing columns that the model expects
            $table->text('description')->nullable()->after('name');
            $table->string('supplier')->nullable()->after('category');
            $table->string('location')->nullable()->after('supplier');
            $table->decimal('unit_price', 8, 2)->nullable()->after('location');
            $table->unsignedBigInteger('last_updated_by')->nullable()->after('unit_price');
            $table->string('status')->default('available')->after('last_updated_by');

            // Add foreign key for last_updated_by
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Remove foreign key first
            $table->dropForeign(['last_updated_by']);

            // Remove added columns
            $table->dropColumn(['description', 'supplier', 'location', 'unit_price', 'last_updated_by', 'status']);

            // Rename columns back
            $table->renameColumn('reorder_point', 'minimum_stock');
            $table->renameColumn('name', 'item_name');
        });
    }
};
