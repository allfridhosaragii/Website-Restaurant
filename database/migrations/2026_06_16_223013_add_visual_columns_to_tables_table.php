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
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('table_layout_id')->nullable()->constrained('table_layouts')->onDelete('set null');
            $table->integer('position_x')->default(0); // koordinat X di grid
            $table->integer('position_y')->default(0); // koordinat Y di grid
            $table->integer('width')->default(1); // lebar (grid cell)
            $table->integer('height')->default(1); // tinggi (grid cell)
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning'])->default('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['table_layout_id']);
            $table->dropColumn(['table_layout_id', 'position_x', 'position_y', 'width', 'height', 'status']);
        });
    }
};
