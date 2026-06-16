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
        Schema::create('table_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Lantai 1", "Lantai 2", "Outdoor"
            $table->integer('grid_width')->default(20); // 20 kolom
            $table->integer('grid_height')->default(15); // 15 baris
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_layouts');
    }
};
