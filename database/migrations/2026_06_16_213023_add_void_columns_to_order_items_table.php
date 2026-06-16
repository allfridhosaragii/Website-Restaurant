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
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('status', ['active', 'voided'])->default('active')->after('subtotal');
            $table->string('void_reason')->nullable()->after('status');
            $table->foreignId('voided_by')->nullable()->constrained('users')->onDelete('set null')->after('void_reason');
            $table->timestamp('voided_at')->nullable()->after('voided_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['voided_by']);
            $table->dropColumn(['status', 'void_reason', 'voided_by', 'voided_at']);
        });
    }
};
