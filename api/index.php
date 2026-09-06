<?php

declare(strict_types=1);

// 1. Paksa PHP menampilkan semua error ke layar
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    define('LARAVEL_START', microtime(true));

    // 2. Siapkan storage di /tmp
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

    // 3. Muat autoloader & bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    if (method_exists($app, 'useStoragePath')) {
        $app->useStoragePath($storage);
    }

    // 4. Tangani request
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
    // 5. JIKA LARAVEL CRASH, PAKSA CETAK DETAIL ERROR KE BROWSER
    http_response_code(200); // Trik: beri status 200 agar tidak dicegat halaman 500 Vercel
    header('Content-Type: text/html; charset=utf-8');
    
    echo '<!DOCTYPE html><html><head><title>Laravel Runtime Error</title>';
    echo '<style>';
    echo 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 32px; line-height: 1.6; }';
    echo '.container { max-width: 900px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border-left: 6px solid #ef4444; }';
    echo 'h1 { color: #f87171; font-size: 22px; margin-top: 0; }';
    echo '.location { color: #94a3b8; font-size: 14px; margin-bottom: 20px; }';
    echo 'pre { background: #0b0f19; padding: 16px; border-radius: 8px; overflow-x: auto; color: #38bdf8; font-size: 13px; }';
    echo '</style></head><body>';
    echo '<div class="container">';
    echo '<h1>⚠️ ' . htmlspecialchars($e->getMessage()) . '</h1>';
    echo '<div class="location"><b>Error di file:</b> ' . htmlspecialchars($e->getFile()) . ' <b>(Baris: ' . $e->getLine() . ')</b></div>';
    echo '<h3>Stack Trace:</h3>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div></body></html>';
    exit;
}