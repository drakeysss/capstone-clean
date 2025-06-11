<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('feedback')) {
            Schema::drop('feedback');
        }
    }

    public function down()
    {
        // No need to recreate the table in down() as it will be handled by the create_feedback_table migration
    }
}; 