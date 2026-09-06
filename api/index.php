<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Diagnostic test: Akses https://domain-kamu.vercel.app/?test=1
if (isset($_GET['test'])) {
    header('Content-Type: text/plain');
    echo "=== VERCEL RUNTIME HEALTH ===\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Vendor autoload: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'EXISTS' : 'MISSING') . "\n";
    echo "Bootstrap app: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'EXISTS' : 'MISSING') . "\n";
    echo "/tmp writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
    echo "Loaded extensions: " . implode(', ', get_loaded_extensions()) . "\n";
    exit;
}

try {
    define('LARAVEL_START', microtime(true));

    // Siapkan storage writable di /tmp
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

    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('VIEW_COMPILED_PATH=' . $storage . '/framework/views');
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('LOG_CHANNEL=stderr');

    // Autoload
    require __DIR__ . '/../vendor/autoload.php';

    // Bootstrap app
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    if (method_exists($app, 'useStoragePath')) {
        $app->useStoragePath($storage);
    }

    // Capture & Handle Request
    $request = \Illuminate\Http\Request::capture();

    if ($app instanceof \Illuminate\Foundation\Application && method_exists($app, 'handleRequest')) {
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
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body style="font-family:monospace;background:#1a202c;color:#fff;padding:24px;">';
    echo '<h2 style="color:#f56565;">Runtime Exception: ' . htmlspecialchars($e->getMessage()) . '</h2>';
    echo '<p><b>File:</b> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<pre style="background:#2d3748;padding:16px;border-radius:6px;overflow-x:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}