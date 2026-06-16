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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'menu_id']);
            $table->string('signature')->default('')->after('menu_id');
            $table->json('modifiers')->nullable()->after('signature');
            $table->unique(['user_id', 'menu_id', 'signature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'menu_id', 'signature']);
            $table->dropColumn(['signature', 'modifiers']);
            $table->unique(['user_id', 'menu_id']);
        });
    }
};
