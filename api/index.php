<?php

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

// Setup folder writable di /tmp khusus serverless Vercel
$storage = '/tmp/storage';
$dirs = [
    $storage . '/framework/views',
    $storage . '/framework/cache/data',
    $storage . '/framework/sessions',
    $storage . '/logs',
    $storage . '/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

putenv('APP_STORAGE=' . $storage);
putenv('VIEW_COMPILED_PATH=' . $storage . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');

// Autoload composer
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel Application
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath($storage);
}

// Jalankan HTTP Kernel standar Laravel
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);