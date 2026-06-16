<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Nullable user_id means it's a global voucher. If set, it's owned by this user (e.g. redeemed)
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            
            // To support 'free_item' voucher type
            // First we need to alter enum for type, or we just add 'free_item' if using string
            // type is already varchar(255) based on db:table output!
            
            $table->foreignId('free_menu_id')->nullable()->after('menu_id')->constrained('menus')->nullOnDelete();
            
            // Usage limit per user (0 means unlimited per user)
            $table->integer('max_usage_per_user')->default(0)->after('usage_limit');
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['free_menu_id']);
            $table->dropColumn(['user_id', 'free_menu_id', 'max_usage_per_user']);
        });
    }
};
