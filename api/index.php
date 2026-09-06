<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Diagnostic Cepat (Akses: ?test=1)
if (isset($_GET['test'])) {
    header('Content-Type: text/plain');
    echo "=== PHP RUNTIME OK ===\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Vendor Autoload: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "\n";
    echo "Bootstrap File: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO') . "\n";
    echo "/tmp Writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
    exit;
}

try {
    define('LARAVEL_START', microtime(true));

    // 2. Siapkan Folder Writable di /tmp
    $storagePath = '/tmp/storage';
    $dirs = [
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
        $storagePath . '/app/public',
        '/tmp/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // 3. Set Environment Variable Storage & Driver Serverless
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('LOG_CHANNEL=stderr');

    // 4. Autoload Composer
    require __DIR__ . '/../vendor/autoload.php';

    // 5. Bootstrap Laravel App
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Set Storage Path langsung pada container instance
    $app->useStoragePath($storagePath);

    // 6. Jalankan Request & Response
    $request = \Illuminate\Http\Request::capture();
    $response = $app->handleRequest($request);
    $response->send();

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><body style="font-family:monospace;background:#1a202c;color:#fff;padding:24px;">';
    echo '<h2 style="color:#f56565;">Runtime Exception: ' . htmlspecialchars($e->getMessage()) . '</h2>';
    echo '<p><b>File:</b> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<pre style="background:#2d3748;padding:16px;border-radius:6px;overflow-x:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}