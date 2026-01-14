<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [];

        // VIP Zone (Tables 1-5) - Premium, larger
        $tables[] = ['number' => 1, 'capacity' => 8, 'zone' => 'vip', 'shape' => 'rectangle', 'is_premium' => true];
        $tables[] = ['number' => 2, 'capacity' => 8, 'zone' => 'vip', 'shape' => 'rectangle', 'is_premium' => true];
        $tables[] = ['number' => 3, 'capacity' => 6, 'zone' => 'vip', 'shape' => 'rectangle', 'is_premium' => true];
        $tables[] = ['number' => 4, 'capacity' => 6, 'zone' => 'vip', 'shape' => 'rectangle', 'is_premium' => true];
        $tables[] = ['number' => 5, 'capacity' => 6, 'zone' => 'vip', 'shape' => 'rectangle', 'is_premium' => true];

        // Main Zone (Tables 6-21) - Standard dining
        for ($i = 6; $i <= 13; $i++) {
            $tables[] = ['number' => $i, 'capacity' => 4, 'zone' => 'main', 'shape' => 'square', 'is_premium' => false];
        }
        for ($i = 14; $i <= 21; $i++) {
            $tables[] = ['number' => $i, 'capacity' => 2, 'zone' => 'main', 'shape' => 'round', 'is_premium' => false];
        }

        // Window Zone (Tables 22-27) - Couple/romantic
        for ($i = 22; $i <= 27; $i++) {
            $tables[] = ['number' => $i, 'capacity' => 2, 'zone' => 'window', 'shape' => 'round', 'is_premium' => false];
        }

        foreach ($tables as $table) {
            Table::updateOrCreate(
                ['number' => $table['number']],
                $table
            );
        }
    }
}
