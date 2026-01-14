<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('daily_stock')->default(50)->after('is_available');
            $table->integer('max_daily_stock')->default(50)->after('daily_stock');
            $table->date('stock_updated_at')->nullable()->after('max_daily_stock');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['daily_stock', 'max_daily_stock', 'stock_updated_at']);
        });
    }
};
