<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip for SQLite (doesn't support MODIFY COLUMN)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        // Modify ENUM to include 'offline' value
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'suspended', 'blocked', 'offline') DEFAULT 'active'");
    }

    public function down(): void
    {
        // Revert back to original ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'suspended', 'blocked') DEFAULT 'active'");
    }
};
