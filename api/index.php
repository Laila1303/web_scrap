<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat direktori sementara di /tmp
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

// 2. Set environment paths
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Set CA Bundle path untuk Linux/Vercel
if (file_exists('/etc/pki/tls/certs/ca-bundle.crt')) {
    putenv('MYSQL_ATTR_SSL_CA=/etc/pki/tls/certs/ca-bundle.crt');
    $_ENV['MYSQL_ATTR_SSL_CA'] = '/etc/pki/tls/certs/ca-bundle.crt';
} elseif (file_exists('/etc/ssl/certs/ca-certificates.crt')) {
    putenv('MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt');
    $_ENV['MYSQL_ATTR_SSL_CA'] = '/etc/ssl/certs/ca-certificates.crt';
}

// 3. Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 4. Inisialisasi Aplikasi Laravel & Storage
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// 5. Tangani Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);