<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'week_cycle')) {
                $table->integer('week_cycle')->default(1)->after('id');
            }
            if (!Schema::hasColumn('menus', 'day')) {
                $table->string('day')->after('week_cycle');
            }
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['week_cycle', 'day']);
        });
    }
};
