<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Diagnostic / Health Check Endpoint
if (isset($_GET['test']) || (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/health-check'))) {
    header('Content-Type: text/plain');
    echo "=== VERCEL PHP DIAGNOSTIC ===\n";
    echo 'PHP Version: ' . PHP_VERSION . "\n";
    echo 'Vendor autoload exists: ' . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "\n";
    echo 'Bootstrap app exists: ' . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO') . "\n";
    echo '/tmp writable: ' . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
    echo 'Loaded extensions: ' . implode(', ', get_loaded_extensions()) . "\n";
    exit;
}

try {
    define('LARAVEL_START', microtime(true));

    // 2. Siapkan Folder Dinamis di /tmp (Read-Write Storage)
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
            @mkdir($dir, 0777, true);
        }
    }

    // 3. Set Serverless-Friendly Environment Variable
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('LOG_CHANNEL=stderr');

    // 4. Load Composer Autoload
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        throw new RuntimeException('vendor/autoload.php tidak ditemukan di server Vercel.');
    }
    require $autoload;

    // 5. Load App Instance
    $appFile = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($appFile)) {
        throw new RuntimeException("bootstrap/app.php tidak ditemukan di: {$appFile}");
    }
    $app = require_once $appFile;

    // Set storage path ke /tmp jika didukung
    if (method_exists($app, 'useStoragePath')) {
        $app->useStoragePath('/tmp/storage');
    }

    // 6. Handle Request
    $request = \Illuminate\Http\Request::capture();
    
    if (method_exists($app, 'handleRequest')) {
        $app->handleRequest($request);
    } else {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Server Error</title>';
    echo '<style>body{font-family:monospace;padding:24px;background:#1a202c;color:#e2e8f0;}h1{color:#f56565;font-size:18px;}pre{background:#2d3748;padding:12px;border-radius:6px;overflow-x:auto;}</style></head><body>';
    echo '<h1>Exception: ' . htmlspecialchars($e->getMessage()) . '</h1>';
    echo '<p><b>File:</b> ' . htmlspecialchars($e->getFile()) . ':' . htmlspecialchars((string) $e->getLine()) . '</p>';
    echo '<h3>Stack Trace:</h3>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}