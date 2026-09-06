<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Diagnostic / Health Check Endpoint (Akses: ?test=1)
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

    // 4. Autoload Composer
    require __DIR__ . '/../vendor/autoload.php';

    // 5. Bootstrap Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 6. Tangani Request Secara Standar Kernel
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = \Illuminate\Http\Request::capture()
    );

    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Runtime Exception</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}