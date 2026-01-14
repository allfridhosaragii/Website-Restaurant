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
        // Add points column to users table
        if (!Schema::hasColumn('users', 'points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('points')->default(0)->after('password');
            });
        }

        // Create point_transactions table
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points'); // Can be positive (earn) or negative (redeem)
            $table->string('type'); // 'order', 'reservation', 'redeem', 'bonus', 'referral'
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');

        if (Schema::hasColumn('users', 'points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('points');
            });
        }
    }
};
