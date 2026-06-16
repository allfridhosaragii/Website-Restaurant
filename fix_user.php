<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = Illuminate\Support\Facades\DB::table('users')
    ->where('email', 'pedoprimasaragi@gmail.com')
    ->update([
        'password' => Illuminate\Support\Facades\Hash::make('password'),
        'is_admin' => 1
    ]);

if ($updated) {
    echo "Password and admin status updated.\n";
} else {
    echo "User not found or no changes made.\n";
}
