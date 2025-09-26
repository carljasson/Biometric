<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('config:cache');
    
    echo "✅ Laravel cache & config cleared!";
} catch (Throwable $e) {
    echo "❌ Error: " . $e->getMessage();
}
