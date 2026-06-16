<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\SupplierDebt;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $users = [
            ['name' => 'Admin', 'email' => 'admin@pos.com', 'password' => Hash::make('password'), 'role' => 'admin', 'is_admin' => true, 'status' => 'active', 'membership_tier' => 'bronze', 'deposit_balance' => 0],
            ['name' => 'Manager', 'email' => 'manager@pos.com', 'password' => Hash::make('password'), 'role' => 'admin', 'is_admin' => true, 'status' => 'active', 'membership_tier' => 'bronze', 'deposit_balance' => 0],
            ['name' => 'Kasir', 'email' => 'kasir@pos.com', 'password' => Hash::make('password'), 'role' => 'cashier', 'is_admin' => false, 'status' => 'active', 'membership_tier' => 'bronze', 'deposit_balance' => 0],
            ['name' => 'Waiter', 'email' => 'waiter@pos.com', 'password' => Hash::make('password'), 'role' => 'waiter', 'is_admin' => false, 'status' => 'active', 'membership_tier' => 'bronze', 'deposit_balance' => 0],
            ['name' => 'Customer', 'email' => 'customer@pos.com', 'password' => Hash::make('password'), 'role' => 'customer', 'is_admin' => false, 'status' => 'active', 'membership_tier' => 'bronze', 'deposit_balance' => 500000],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }
        $customer = User::where('email', 'customer@pos.com')->first();
        $admin = User::where('email', 'admin@pos.com')->first();

        // 2. Categories (Not a model, just strings for menu category field)
        $categories = ['Makanan Utama', 'Minuman', 'Dessert', 'Snack', 'Paket'];

        // 3. Menus
        $menusData = [
            ['name' => 'Nasi Goreng Spesial', 'price' => 25000, 'category' => $categories[0], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_food.png'],
            ['name' => 'Mie Ayam Jamur', 'price' => 20000, 'category' => $categories[0], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_food.png'],
            ['name' => 'Ayam Penyet', 'price' => 22000, 'category' => $categories[0], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_food.png'],
            ['name' => 'Sate Ayam', 'price' => 30000, 'category' => $categories[0], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_food.png'],
            ['name' => 'Es Teh Manis', 'price' => 5000, 'category' => $categories[1], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_drink.png'],
            ['name' => 'Jus Alpukat', 'price' => 15000, 'category' => $categories[1], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_drink.png'],
            ['name' => 'Kopi Hitam', 'price' => 10000, 'category' => $categories[1], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_drink.png'],
            ['name' => 'Puding Coklat', 'price' => 12000, 'category' => $categories[2], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_dessert.png'],
            ['name' => 'Es Krim Sundae', 'price' => 18000, 'category' => $categories[2], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_dessert.png'],
            ['name' => 'Kentang Goreng', 'price' => 15000, 'category' => $categories[3], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_snack.png'],
            ['name' => 'Pisang Bakar', 'price' => 15000, 'category' => $categories[3], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_snack.png'],
            ['name' => 'Paket Ayam Geprek', 'price' => 28000, 'category' => $categories[4], 'image_url' => 'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/placeholder_food.png'],
        ];

        $menus = [];
        foreach ($menusData as $m) {
            $menu = Menu::firstOrCreate(
                ['name' => $m['name']],
                [
                    'slug' => \Str::slug($m['name']),
                    'description' => 'Deskripsi untuk ' . $m['name'],
                    'price' => $m['price'],
                    'category' => $m['category'],
                    'daily_stock' => 100,
                    'max_daily_stock' => 100,
                    'stock_updated_at' => Carbon::now(),
                    'is_available' => true,
                    'image_url' => $m['image_url']
                ]
            );
            $menus[] = $menu;

            if (str_contains($m['name'], 'Nasi Goreng') || str_contains($m['name'], 'Mie Ayam')) {
                $modifier = \App\Models\MenuModifier::firstOrCreate([
                    'menu_id' => $menu->id,
                    'name' => 'Level Pedas'
                ], [
                    'type' => 'single',
                    'is_required' => true,
                    'max_select' => 1
                ]);
                \App\Models\MenuModifierOption::firstOrCreate(['menu_modifier_id' => $modifier->id, 'name' => 'Sedang'], ['price' => 0]);
                \App\Models\MenuModifierOption::firstOrCreate(['menu_modifier_id' => $modifier->id, 'name' => 'Pedas'], ['price' => 0]);

                $modifier2 = \App\Models\MenuModifier::firstOrCreate([
                    'menu_id' => $menu->id,
                    'name' => 'Tambahan'
                ], [
                    'type' => 'multiple',
                    'is_required' => false,
                    'max_select' => 5
                ]);
                \App\Models\MenuModifierOption::firstOrCreate(['menu_modifier_id' => $modifier2->id, 'name' => 'Telur Dadar'], ['price' => 5000]);
                \App\Models\MenuModifierOption::firstOrCreate(['menu_modifier_id' => $modifier2->id, 'name' => 'Kerupuk'], ['price' => 2000]);
            }
        }

        // 4. Tables
        $tables = [];
        for ($i = 1; $i <= 8; $i++) {
            $tables[] = Table::firstOrCreate(
                ['number' => $i],
                [
                    'capacity' => ($i % 2 == 0) ? 4 : 2,
                    'status' => 'available',
                    'position_x' => ($i % 4) * 2,
                    'position_y' => floor(($i - 1) / 4) * 2,
                    'width' => 1,
                    'height' => 1,
                    'shape' => 'rectangle'
                ]
            );
        }

        // 5. Orders (Last 7 Days)
        $statuses = ['completed', 'completed', 'completed', 'completed', 'processing', 'pending'];
        $types = ['dine_in', 'take_away'];
        for ($i = 1; $i <= 25; $i++) {
            $status = $statuses[array_rand($statuses)];
            $type = $types[array_rand($types)];
            $table = ($type == 'dine_in') ? $tables[array_rand($tables)]->id : null;
            $date = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23));

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => rand(0, 1) ? $customer->id : null,
                'table_id' => $table,
                'guest_name' => 'Guest ' . $i,
                'status' => $status,
                'type' => $type,
                'payment_status' => ($status == 'completed' || $status == 'processing') ? 'paid' : 'pending',
                'payment_method' => 'cash',
                'total' => 0,
                'tax' => 0,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            $total = 0;
            $itemsCount = rand(1, 4);
            for ($j = 0; $j < $itemsCount; $j++) {
                $menu = $menus[array_rand($menus)];
                $qty = rand(1, 3);
                $subtotal = $menu->price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'quantity' => $qty,
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                    'kitchen_status' => ($status == 'completed') ? 'served' : (($status == 'processing') ? 'cooking' : 'pending'),
                    'modifiers' => null
                ]);
            }

            $order->update([
                'total' => $total,
                'tax' => $total * 0.1
            ]);
        }

        // 6. Reservations
        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::now()->addDays(rand(0, 1))->addHours(rand(2, 6));
            Reservation::create([
                'user_id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => '08123456789' . $i,
                'table_id' => $tables[array_rand($tables)]->id,
                'date' => $date->toDateString(),
                'time' => $date->toTimeString(),
                'guests' => rand(2, 6),
                'status' => 'accepted',
                'notes' => 'Mohon meja dibersihkan',
                'deposit_amount' => 50000
            ]);
        }

        // 7. Reviews
        $completedOrders = Order::where('status', 'completed')->whereNotNull('user_id')->limit(6)->get();
        foreach ($completedOrders as $order) {
            Review::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'rating' => rand(3, 5),
                'comment' => 'Makanannya enak, pelayanan memuaskan!',
                'is_approved' => true
            ]);
        }

        // 8. Expense Categories
        $expCat = [
            ['name' => 'Bahan Baku', 'color' => '#198754'],
            ['name' => 'Gaji Karyawan', 'color' => '#0dcaf0'],
            ['name' => 'Utilitas', 'color' => '#ffc107'],
            ['name' => 'Sewa Tempat', 'color' => '#dc3545'],
            ['name' => 'Lainnya', 'color' => '#6c757d'],
        ];
        $expCategories = [];
        foreach ($expCat as $cat) {
            $expCategories[] = ExpenseCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 9. Expenses
        for ($i = 1; $i <= 10; $i++) {
            Expense::create([
                'title' => 'Pengeluaran ' . $i,
                'category_id' => $expCategories[array_rand($expCategories)]->id,
                'amount' => rand(50000, 500000),
                'date' => Carbon::now()->subDays(rand(0, 10)),
                'description' => 'Biaya operasional',
                'created_by' => $admin->id
            ]);
        }

        // 10. Supplier Debts
        $supplierDebts = [
            ['supplier_name' => 'PT Daging Sapi Makmur', 'title' => 'Pembelian Daging 20kg', 'amount' => 2000000, 'paid_amount' => 0, 'remaining_amount' => 2000000, 'due_date' => Carbon::now()->addDays(2), 'status' => 'unpaid'],
            ['supplier_name' => 'Sayur Segar Jaya', 'title' => 'Sayuran Mingguan', 'amount' => 500000, 'paid_amount' => 200000, 'remaining_amount' => 300000, 'due_date' => Carbon::now()->addDays(5), 'status' => 'partial'],
            ['supplier_name' => 'Grosir Beras Kita', 'title' => 'Beras 5 Karung', 'amount' => 750000, 'paid_amount' => 750000, 'remaining_amount' => 0, 'due_date' => Carbon::now()->subDays(1), 'status' => 'paid'],
        ];
        foreach ($supplierDebts as $debt) {
            $debt['created_by'] = $admin->id;
            SupplierDebt::create($debt);
        }
    }
}
