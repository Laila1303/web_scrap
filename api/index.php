<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat folder penyimpanan dinamis di /tmp (satu-satunya folder writeable di Vercel)
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Set environment runtime
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');

// 3. Autoload & Bootstrap
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// 4. Handle Request
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
