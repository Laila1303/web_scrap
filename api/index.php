<?php

declare(strict_types=1);

// 1. Setup direktori writable di /tmp
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

// 2. Override Environment Variables
$_ENV['APP_STORAGE'] = $storage;
$_ENV['VIEW_COMPILED_PATH'] = $storage . '/framework/views';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

putenv("APP_STORAGE={$storage}");
putenv("VIEW_COMPILED_PATH={$storage}/framework/views");
putenv("LOG_CHANNEL=stderr");
putenv("APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php");
putenv("APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php");
putenv("APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php");
putenv("APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php");
putenv("APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php");

// 3. Inisialisasi & Paksa Storage Path Laravel
require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storage);

// 4. Handle Request
$app->handleRequest(\Illuminate\Http\Request::capture());