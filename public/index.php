<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>RAW EXCEPTION:</h1>";
    echo "<b>Message:</b> " . $e->getMessage() . "<br>";
    echo "<b>Class:</b> " . get_class($e) . "<br>";
    echo "<b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}