<?php

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

// 1. Buat direktori /tmp untuk writeable storage Laravel
$baseTmp = '/tmp/storage';
foreach ([
    $baseTmp . '/framework/views',
    $baseTmp . '/framework/cache/data',
    $baseTmp . '/framework/sessions',
    $baseTmp . '/logs',
    $baseTmp . '/app/public',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set environment runtime
putenv('VIEW_COMPILED_PATH=' . $baseTmp . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');

// 3. Autoload & Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Set storage path ke /tmp
$app->useStoragePath($baseTmp);

// 5. Tangani request & kirim response
$request = \Illuminate\Http\Request::capture();
$response = $app->handleRequest($request);
$response->send();