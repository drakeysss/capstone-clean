<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add missing columns to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->string('role')->default('user')->after('email');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('role');
                }
            });
        }

        // Add foreign keys to menus table
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (!Schema::hasColumn('menus', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('menus', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                }
            });
        }

        // Add indexes for better performance
        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                if (!Schema::hasIndex('admin_settings', 'admin_settings_setting_key_index')) {
                    $table->index('setting_key');
                }
            });
        }

        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (!Schema::hasIndex('menus', 'menus_meal_type_index')) {
                    $table->index('meal_type');
                }
                if (!Schema::hasIndex('menus', 'menus_date_index')) {
                    $table->index('date');
                }
            });
        }
    }

    public function down()
    {
        // Remove indexes
        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                if (Schema::hasIndex('admin_settings', 'admin_settings_setting_key_index')) {
                    $table->dropIndex(['setting_key']);
                }
            });
        }

        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (Schema::hasIndex('menus', 'menus_meal_type_index')) {
                    $table->dropIndex(['meal_type']);
                }
                if (Schema::hasIndex('menus', 'menus_date_index')) {
                    $table->dropIndex(['date']);
                }
            });
        }

        // Remove foreign keys
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (Schema::hasColumn('menus', 'created_by')) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                }
                if (Schema::hasColumn('menus', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
            });
        }

        // Remove columns from users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role')) {
                    $table->dropColumn('role');
                }
                if (Schema::hasColumn('users', 'is_active')) {
                    $table->dropColumn('is_active');
                }
            });
        }
    }
}; 