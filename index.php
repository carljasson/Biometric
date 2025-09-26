<?php

define('LARAVEL_START', microtime(true));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// If app is in maintenance mode...
if (file_exists(__DIR__.'/storage/framework/maintenance.php')) {
    require __DIR__.'/storage/framework/maintenance.php';
}

// Load Composer
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);
