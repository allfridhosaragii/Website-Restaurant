<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->default(150000)->after('status');
            $table->enum('deposit_status', ['pending', 'paid', 'refunded'])->default('pending')->after('deposit_amount');
            $table->string('deposit_proof')->nullable()->after('deposit_status');
        });
    }
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'deposit_status', 'deposit_proof']);
        });
    }
};