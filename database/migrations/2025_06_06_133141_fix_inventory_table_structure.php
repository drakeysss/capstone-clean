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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('inventory', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('inventory', 'reorder_point')) {
                $table->decimal('reorder_point', 10, 2)->default(10)->after('minimum_stock');
            }
            if (!Schema::hasColumn('inventory', 'supplier')) {
                $table->string('supplier')->nullable()->after('reorder_point');
            }
            if (!Schema::hasColumn('inventory', 'location')) {
                $table->string('location')->nullable()->after('supplier');
            }
            if (!Schema::hasColumn('inventory', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('location');
            }
            if (!Schema::hasColumn('inventory', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('inventory', 'last_updated_by')) {
                $table->foreignId('last_updated_by')->nullable()->constrained('users')->after('unit_price');
            }
            if (!Schema::hasColumn('inventory', 'status')) {
                $table->enum('status', ['available', 'low_stock', 'out_of_stock', 'expired'])->default('available')->after('last_updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'reorder_point', 'supplier', 'location',
                'unit_price', 'description', 'status'
            ]);
            if (Schema::hasColumn('inventory', 'last_updated_by')) {
                $table->dropForeign(['last_updated_by']);
                $table->dropColumn('last_updated_by');
            }
        });
    }
};
