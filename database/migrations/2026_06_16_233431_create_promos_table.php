<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Beli 2 Nasi Goreng Gratis 1 Es Teh"
            $table->enum('type', ['buy_x_get_y', 'discount_percent', 'discount_fixed', 'bundle'])->default('buy_x_get_y');
            $table->foreignId('buy_menu_id')->constrained('menus')->onDelete('cascade'); // menu yang harus dibeli
            $table->integer('buy_quantity'); // minimal berapa
            $table->foreignId('get_menu_id')->nullable()->constrained('menus')->onDelete('cascade'); // menu gratis
            $table->integer('get_quantity')->default(1); // gratis berapa
            $table->enum('get_type', ['free', 'discount'])->default('free'); // gratis atau diskon
            $table->decimal('get_discount_percent', 5, 2)->nullable(); // kalau get_type = discount
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
