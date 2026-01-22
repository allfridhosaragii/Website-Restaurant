<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('page_url');
            $table->string('page_title')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('device_type')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_email')->nullable();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->timestamp('last_heartbeat')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['session_id', 'page_url']);
            $table->index('is_active');
            $table->index('entry_time');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('site_visitors');
    }
};