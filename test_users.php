<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = ['admin@pos.com', 'manager@pos.com', 'kasir@pos.com', 'waiter@pos.com', 'customer@pos.com'];
foreach($emails as $email) {
    $u = \App\Models\User::where('email', $email)->first();
    if($u) {
        echo $email . ' -> role: ' . $u->role . ' | is_admin: ' . $u->is_admin . "\n";
    } else {
        echo $email . " NOT FOUND\n";
    }
}
