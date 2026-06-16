<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#000000');
            $table->timestamps();
        });

        // Add category_id to menus
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        });

        // Migrate existing string categories to the categories table
        $menus = DB::table('menus')->get();
        foreach ($menus as $menu) {
            if (!empty($menu->category)) {
                // Find or create category
                $slug = Str::slug($menu->category);
                $category = DB::table('categories')->where('slug', $slug)->first();
                if (!$category) {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => $menu->category,
                        'slug' => $slug,
                        'color' => '#10B981', // Default emerald green
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $categoryId = $category->id;
                }
                
                DB::table('menus')->where('id', $menu->id)->update([
                    'category_id' => $categoryId
                ]);
            }
        }

        // Optionally, drop the string category column
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('category')->default('Makanan');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
