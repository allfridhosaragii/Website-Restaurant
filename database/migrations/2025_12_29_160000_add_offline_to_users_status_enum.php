<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'suspended', 'blocked', 'offline') DEFAULT 'active'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'suspended'::character varying, 'blocked'::character varying, 'offline'::character varying]::text[]))");
        }
    }
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'suspended', 'blocked') DEFAULT 'active'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'suspended'::character varying, 'blocked'::character varying]::text[]))");
        }
    }
};