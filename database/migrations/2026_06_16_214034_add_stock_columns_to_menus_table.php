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
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('price');
            $table->integer('min_stock')->default(5)->after('stock');
            $table->boolean('is_active')->default(true)->after('min_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['stock', 'min_stock', 'is_active']);
        });
    }
};
