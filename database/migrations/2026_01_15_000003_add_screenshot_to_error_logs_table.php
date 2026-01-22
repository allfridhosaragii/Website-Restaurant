<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('error_logs', function (Blueprint $table) {
            $table->string('screenshot_url')->nullable()->after('request_data');
            $table->string('user_agent')->nullable()->after('screenshot_url');
            $table->string('browser')->nullable()->after('user_agent');
            $table->string('device_type')->nullable()->after('browser');
            $table->string('screen_size')->nullable()->after('device_type');
        });
    }
    public function down(): void
    {
        Schema::table('error_logs', function (Blueprint $table) {
            $table->dropColumn(['screenshot_url', 'user_agent', 'browser', 'device_type', 'screen_size']);
        });
    }
};