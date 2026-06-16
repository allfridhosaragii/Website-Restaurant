<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Starting data import to Supabase...\n";

// Disable foreign key checks for PostgreSQL temporarily
DB::statement('SET session_replication_role = replica;');

// Clear existing
DB::table('order_items')->truncate();
DB::table('orders')->truncate();
DB::table('menus')->truncate();
DB::table('activity_logs')->truncate();
DB::table('users')->truncate();

// 1. Users
DB::table('users')->insert([
    ['id'=>1, 'name'=>'Super Admin', 'email'=>'admin@super.admin', 'google_id'=>null, 'is_admin'=>1, 'status'=>'active', 'password'=>'$2y$12$nqoSylweT02q9YeVPiQkS.IGVF8q6S./RC7l7kfiFU/E4xQnsw3E.', 'remember_token'=>null, 'created_at'=>'2025-12-18 01:35:13', 'updated_at'=>'2025-12-18 01:45:34'],
    ['id'=>2, 'name'=>'Allfridho Saragi', 'email'=>'pedoprimasaragi@gmail.com', 'google_id'=>'107059635762742983559', 'is_admin'=>0, 'status'=>'active', 'password'=>null, 'remember_token'=>'df9vPihjZTF5hdoX5FOqFKs8Hv2oBcJp10uF22JpONzvgr3jOmvyvyo6kBXA', 'created_at'=>'2025-12-18 01:39:46', 'updated_at'=>'2025-12-18 01:39:46'],
    ['id'=>3, 'name'=>'bernard prawira', 'email'=>'bernardprawira54@gmail.com', 'google_id'=>'111152783289894628680', 'is_admin'=>0, 'status'=>'active', 'password'=>null, 'remember_token'=>'wBWwpCiYbjiK8fEr5qhAp6S1LSNAU0Bpxh74oGjjn2BNTGT0QzgN4GOeGxFn', 'created_at'=>'2025-12-18 16:04:01', 'updated_at'=>'2025-12-18 16:04:01'],
    ['id'=>4, 'name'=>'Haidar Mirza', 'email'=>'haiidarmirza8289@gmail.com', 'google_id'=>'101354018129439778500', 'is_admin'=>0, 'status'=>'active', 'password'=>null, 'remember_token'=>'W7Fx3XLe9WtZVjbD0ufWrRSQH0Jfl8mn2ThwwdGOPWGiwAf3MeVPjz6ibZEl', 'created_at'=>'2025-12-24 07:14:19', 'updated_at'=>'2025-12-24 07:14:19']
]);
echo "Users imported.\n";

// 2. Menus
DB::table('menus')->insert([
    ['id'=>1, 'name'=>'Rendang Sapi Premium', 'slug'=>'rendang-sapi-premium', 'description'=>'Daging sapi pilihan dengan bumbu rempah khas Padang.', 'price'=>85000.00, 'image_url'=>'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766036787/culinaire/menus/v9rjvyfleswev2qhcdtt.png', 'category'=>'Makanan', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:35', 'updated_at'=>'2025-12-18 05:46:30'],
    ['id'=>2, 'name'=>'Nasi Goreng Spesial', 'slug'=>'nasi-goreng-spesial', 'description'=>'Nasi goreng dengan telur, ayam, dan sayuran segar.', 'price'=>45000.00, 'image_url'=>'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766036734/culinaire/menus/mohzd6b5i9kpcaud6ci7.png', 'category'=>'Makanan', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:36', 'updated_at'=>'2025-12-18 05:45:36'],
    ['id'=>3, 'name'=>'Sate Ayam Madura', 'slug'=>'sate-ayam-madura', 'description'=>'10 tusuk sate dengan bumbu kacang dan lontong.', 'price'=>55000.00, 'image_url'=>'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766024282/culinaire/menus/gyfys8yal8iimzl9onez.png', 'category'=>'Makanan', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:37', 'updated_at'=>'2025-12-18 02:18:04'],
    ['id'=>4, 'name'=>'Mie Goreng Jawa', 'slug'=>'mie-goreng-jawa', 'description'=>'Mie goreng dengan bumbu kecap manis khas Jawa.', 'price'=>42000.00, 'image_url'=>'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=400&h=300&fit=crop', 'category'=>'Nasi & Mie', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:38', 'updated_at'=>'2025-12-18 01:45:38'],
    ['id'=>5, 'name'=>'Papeda Khas Papua', 'slug'=>'papeda-khas-papua', 'description'=>'Makanan khas papua yang sangat terkenal hingga internasional.', 'price'=>35000.00, 'image_url'=>'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766039478/culinaire/menus/kq5xqcprkjjroxhi8xzc.jpg', 'category'=>'Makanan', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:39', 'updated_at'=>'2025-12-18 07:24:29'],
    ['id'=>6, 'name'=>'Ayam Bakar Taliwang', 'slug'=>'ayam-bakar-taliwang', 'description'=>'Ayam bakar dengan bumbu pedas khas Lombok.', 'price'=>65000.00, 'image_url'=>'https://res.cloudinary.com/dh9ysyfit/image/upload/v1766024001/culinaire/menus/k6bplx8apor5wft6gaw5.png', 'category'=>'Makanan', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:40', 'updated_at'=>'2025-12-18 02:13:25'],
    ['id'=>7, 'name'=>'Es Teh Manis', 'slug'=>'es-teh-manis', 'description'=>'Teh manis segar dengan es batu.', 'price'=>15000.00, 'image_url'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop', 'category'=>'Minuman', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:41', 'updated_at'=>'2025-12-18 01:45:41'],
    ['id'=>8, 'name'=>'Es Cendol Durian', 'slug'=>'es-cendol-durian', 'description'=>'Cendol segar dengan topping durian.', 'price'=>25000.00, 'image_url'=>'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop', 'category'=>'Dessert', 'is_available'=>1, 'created_at'=>'2025-12-18 01:45:42', 'updated_at'=>'2025-12-18 01:45:42']
]);
echo "Menus imported.\n";

// 3. Orders
DB::table('orders')->insert([
    ['id'=>1, 'order_number'=>'ORD-251218-2931', 'user_id'=>2, 'type'=>'dine_in', 'table_number'=>'3', 'subtotal'=>45000.00, 'tax'=>4500.00, 'total'=>49500.00, 'status'=>'pending', 'payment_status'=>'pending', 'notes'=>null, 'created_at'=>'2025-12-18 07:17:17', 'updated_at'=>'2025-12-18 07:17:17'],
    ['id'=>2, 'order_number'=>'ORD-251218-5DB8', 'user_id'=>3, 'type'=>'dine_in', 'table_number'=>'VIP', 'subtotal'=>25000.00, 'tax'=>2500.00, 'total'=>27500.00, 'status'=>'pending', 'payment_status'=>'pending', 'notes'=>null, 'created_at'=>'2025-12-18 16:07:40', 'updated_at'=>'2025-12-18 16:07:40'],
    ['id'=>3, 'order_number'=>'ORD-251224-56BD', 'user_id'=>4, 'type'=>'dine_in', 'table_number'=>'3', 'subtotal'=>85000.00, 'tax'=>8500.00, 'total'=>93500.00, 'status'=>'pending', 'payment_status'=>'pending', 'notes'=>null, 'created_at'=>'2025-12-24 07:16:42', 'updated_at'=>'2025-12-24 07:16:42']
]);
echo "Orders imported.\n";

// 4. Order Items
DB::table('order_items')->insert([
    ['id'=>1, 'order_id'=>1, 'menu_id'=>2, 'menu_name'=>'Nasi Goreng Spesial', 'price'=>45000.00, 'quantity'=>1, 'subtotal'=>45000.00, 'created_at'=>'2025-12-18 07:17:18', 'updated_at'=>'2025-12-18 07:17:18'],
    ['id'=>2, 'order_id'=>2, 'menu_id'=>8, 'menu_name'=>'Es Cendol Durian', 'price'=>25000.00, 'quantity'=>1, 'subtotal'=>25000.00, 'created_at'=>'2025-12-18 16:07:40', 'updated_at'=>'2025-12-18 16:07:40'],
    ['id'=>3, 'order_id'=>3, 'menu_id'=>1, 'menu_name'=>'Rendang Sapi Premium', 'price'=>85000.00, 'quantity'=>1, 'subtotal'=>85000.00, 'created_at'=>'2025-12-24 07:16:43', 'updated_at'=>'2025-12-24 07:16:43']
]);
echo "Order Items imported.\n";

// 5. Activity Logs
DB::table('activity_logs')->insert([
    ['id'=>1, 'user_id'=>1, 'action'=>'login', 'description'=>'User logged in', 'ip_address'=>'127.0.0.1', 'created_at'=>'2025-12-18 01:38:37', 'updated_at'=>'2025-12-18 01:38:37'],
    ['id'=>2, 'user_id'=>2, 'action'=>'logout', 'description'=>'User logged out', 'ip_address'=>'127.0.0.1', 'created_at'=>'2025-12-18 01:41:59', 'updated_at'=>'2025-12-18 01:41:59'],
    ['id'=>3, 'user_id'=>1, 'action'=>'login', 'description'=>'User logged in', 'ip_address'=>'127.0.0.1', 'created_at'=>'2025-12-18 01:52:16', 'updated_at'=>'2025-12-18 01:52:16'],
    ['id'=>4, 'user_id'=>1, 'action'=>'logout', 'description'=>'User logged out', 'ip_address'=>'127.0.0.1', 'created_at'=>'2025-12-18 01:55:47', 'updated_at'=>'2025-12-18 01:55:47']
]);
echo "Activity Logs imported (partial).\n";

// Fix Auto Increment sequence in PostgreSQL
DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));");
DB::statement("SELECT setval('menus_id_seq', (SELECT MAX(id) FROM menus));");
DB::statement("SELECT setval('orders_id_seq', (SELECT MAX(id) FROM orders));");
DB::statement("SELECT setval('order_items_id_seq', (SELECT MAX(id) FROM order_items));");
DB::statement("SELECT setval('activity_logs_id_seq', (SELECT MAX(id) FROM activity_logs));");

// Enable foreign key checks
DB::statement('SET session_replication_role = DEFAULT;');

echo "Data import completed successfully!\n";
