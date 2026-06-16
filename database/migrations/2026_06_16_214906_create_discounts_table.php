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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('value', 12, 2);
            $table->enum('scope', ['order', 'item'])->default('order');
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->string('voucher_code')->nullable()->unique();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->time('happy_hour_start')->nullable();
            $table->time('happy_hour_end')->nullable();
            $table->json('days_of_week')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
