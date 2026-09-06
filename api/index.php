<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
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
            @mkdir($dir, 0777, true);
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

    // Fallback Environment Variables jika belum diatur di Vercel Dashboard
    if (! getenv('APP_KEY') && ! isset($_ENV['APP_KEY'])) {
        $appKey = 'base64:17UeYJ9XJRIvHuDZFyj0AhVLEwapOg4wT28tAMd/2ng=';
        putenv("APP_KEY={$appKey}");
        $_ENV['APP_KEY'] = $appKey;
        $_SERVER['APP_KEY'] = $appKey;
    }

    if (! getenv('DB_HOST') && ! isset($_ENV['DB_HOST'])) {
        putenv('DB_CONNECTION=mysql');
        putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
        putenv('DB_PORT=4000');
        putenv('DB_DATABASE=web_scrap');
        putenv('DB_USERNAME=2CWvHmcHm2JVKo8.root');
        putenv('DB_PASSWORD=wzb9P97tSjRYXtYL');
        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
        $_ENV['DB_PORT'] = '4000';
        $_ENV['DB_DATABASE'] = 'web_scrap';
        $_ENV['DB_USERNAME'] = '2CWvHmcHm2JVKo8.root';
        $_ENV['DB_PASSWORD'] = 'wzb9P97tSjRYXtYL';
    }

    if (! getenv('APP_NAME') && ! isset($_ENV['APP_NAME'])) {
        putenv('APP_NAME=Kayla Scrapbook');
        $_ENV['APP_NAME'] = 'Kayla Scrapbook';
    }

    if (! getenv('APP_ENV') && ! isset($_ENV['APP_ENV'])) {
        putenv('APP_ENV=production');
        $_ENV['APP_ENV'] = 'production';
    }

    // 3. Autoload & Bootstrap
    $autoload = __DIR__.'/../vendor/autoload.php';
    if (! file_exists($autoload)) {
        throw new RuntimeException('vendor/autoload.php tidak ditemukan di server Vercel. Pastikan dependencies composer ter-install.');
    }
    require $autoload;

    $appFile = __DIR__.'/../bootstrap/app.php';
    if (! file_exists($appFile)) {
        throw new RuntimeException("bootstrap/app.php tidak ditemukan di: {$appFile}");
    }
    $app = require_once $appFile;
    $app->useStoragePath('/tmp/storage');

    // 4. Handle Request (Laravel 12 Standard)
    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Server Error</title>';
    echo '<style>body{font-family:system-ui,-apple-system,sans-serif;padding:32px;background:#fff5f5;color:#2d3748;}pre{background:#1a202c;color:#e2e8f0;padding:16px;border-radius:8px;overflow:auto;font-size:13px;line-height:1.5;}</style></head><body>';
    echo '<h1 style="color:#e53e3e;">Server Error Terdeteksi</h1>';
    echo '<p><strong>Pesan:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><strong>Lokasi:</strong> '.htmlspecialchars($e->getFile()).' baris '.htmlspecialchars((string) $e->getLine()).'</p>';
    echo '<h3>Trace:</h3>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
    echo '</body></html>';
}
