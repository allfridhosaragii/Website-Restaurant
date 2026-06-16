<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->decimal('min_spent', 12, 2); // minimal total spent untuk tier ini
            $table->decimal('discount_percent', 5, 2)->default(0); // diskon otomatis
            $table->decimal('point_multiplier', 4, 2)->default(1); // 1x, 1.5x, 2x, 3x poin
            $table->text('benefits')->nullable(); // deskripsi benefit
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
