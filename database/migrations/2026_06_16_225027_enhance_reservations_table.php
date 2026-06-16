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
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('end_time')->nullable()->after('time');
            $table->timestamp('reminded_at')->nullable()->after('status');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null')->after('reminded_at');
            $table->timestamp('checked_in_at')->nullable()->after('checked_in_by');
            
            // Note: PostgreSQL ENUM modification is tricky, so we just add string 'current_status' to bypass the old enum, or we can use raw SQL.
            // But actually, in Laravel 11 with PostgreSQL, we can use change() if doctrine/dbal is installed, or drop/add.
            // Let's just drop status and recreate it as string to avoid ENUM issues in Postgres
        });

        // Postgres drop constraint for ENUM if exists (usually not needed if we just drop column, but let's change column type to string safely)
        // Wait, SQLite doesn't support changing ENUM easily. So we add a new column 'reservation_status' and migrate data
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reservation_status')->default('pending')->after('status');
        });
        
        // Migrate old status
        DB::statement("UPDATE reservations SET reservation_status = status::text");

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('reservation_status', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by']);
            $table->dropColumn([
                'end_time', 
                'reminded_at', 
                'checked_in_by', 
                'checked_in_at'
            ]);
            
            // Revert status back to enum if necessary, or leave as string
        });
    }
};
