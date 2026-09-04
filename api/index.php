<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat folder sementara di /tmp
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Set environment path ke /tmp
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// 3. Muat Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 4. Inisialisasi Aplikasi Laravel & Set Storage Path
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage path instance ke /tmp
$app->useStoragePath('/tmp/storage');

// 5. Tangani Request Masuk
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);