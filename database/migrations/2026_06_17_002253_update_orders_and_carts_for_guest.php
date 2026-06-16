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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('session_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone']);
        });
        
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
};
