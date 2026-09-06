<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Diagnostic Endpoint
if (isset($_GET['test'])) {
    header('Content-Type: text/plain');
    echo "=== VERCEL RUNTIME HEALTH ===\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Storage Writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
    echo "Autoload Exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "\n";
    echo "Bootstrap Exists: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO') . "\n";
    exit;
}

try {
    define('LARAVEL_START', microtime(true));

    // 2. Siapkan direktori writable di /tmp
    $dirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/storage/app/public',
        '/tmp/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // 3. Set environment variable storage serverless
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('LOG_CHANNEL=stderr');

    // 4. Autoload Composer & Bootstrap Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Handle Request (Kompatibel Laravel 11/12 & Laravel 10)
    $request = \Illuminate\Http\Request::capture();

    if (method_exists($app, 'handleRequest')) {
        $response = $app->handleRequest($request);
        $response->send();
    } else {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body style="font-family:monospace;background:#1a202c;color:#fff;padding:20px;">';
    echo '<h2 style="color:#f56565;">Runtime Exception</h2>';
    echo '<p><b>Message:</b> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><b>File:</b> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<pre style="background:#2d3748;padding:15px;border-radius:5px;overflow-x:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}