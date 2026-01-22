<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'device_type')) {
                $table->string('device_type')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('activity_logs', 'device_name')) {
                $table->string('device_name')->nullable()->after('device_type');
            }
            if (!Schema::hasColumn('activity_logs', 'browser')) {
                $table->string('browser')->nullable()->after('device_name');
            }
            if (!Schema::hasColumn('activity_logs', 'os')) {
                $table->string('os')->nullable()->after('browser');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'device_name', 'browser', 'os']);
        });
    }
};
