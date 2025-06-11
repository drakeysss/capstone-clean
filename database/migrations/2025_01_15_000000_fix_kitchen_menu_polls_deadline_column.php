<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's see what data exists
        $existingPolls = DB::table('kitchen_menu_polls')->get();
        
        // Change the deadline column from TIME to DATETIME
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            // Drop the old TIME column and create new DATETIME column
            $table->dropColumn('deadline');
        });
        
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            // Add new DATETIME column
            $table->datetime('deadline')->nullable()->after('meal_type');
        });
        
        // Restore data with proper datetime format
        foreach ($existingPolls as $poll) {
            // Convert old time format to datetime
            $pollDate = $poll->poll_date;
            $oldDeadline = $poll->deadline ?? '22:00:00';
            
            // Create full datetime: poll_date + deadline_time
            $newDeadline = $pollDate . ' ' . $oldDeadline;
            
            DB::table('kitchen_menu_polls')
                ->where('id', $poll->id)
                ->update(['deadline' => $newDeadline]);
        }
        
        // Make the column NOT NULL with default
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            $table->datetime('deadline')->default('2025-01-01 22:00:00')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get existing data
        $existingPolls = DB::table('kitchen_menu_polls')->get();
        
        // Change back to TIME column
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
        
        Schema::table('kitchen_menu_polls', function (Blueprint $table) {
            $table->time('deadline')->default('22:00')->after('meal_type');
        });
        
        // Restore data with time only
        foreach ($existingPolls as $poll) {
            // Extract time part from datetime
            $datetime = $poll->deadline;
            $timeOnly = date('H:i:s', strtotime($datetime));
            
            DB::table('kitchen_menu_polls')
                ->where('id', $poll->id)
                ->update(['deadline' => $timeOnly]);
        }
    }
};
