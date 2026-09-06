<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Pastikan error ditangkap dengan jelas
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Endpoint diagnostik ringan jika diakses dengan parameter ?diagnostic=1
if (isset($_GET['diagnostic'])) {
    header('Content-Type: text/plain');
    echo "=== VERCEL PHP DIAGNOSTIC ===\n";
    echo 'PHP Version: '.PHP_VERSION."\n";
    echo 'Vendor Autoload: '.(file_exists(__DIR__.'/../vendor/autoload.php') ? 'EXISTS' : 'MISSING')."\n";
    echo 'Bootstrap App: '.(file_exists(__DIR__.'/../bootstrap/app.php') ? 'EXISTS' : 'MISSING')."\n";
    echo 'cacert.pem: '.(file_exists(__DIR__.'/../cacert.pem') ? 'EXISTS' : 'MISSING')."\n";
    echo '/tmp Writable: '.(is_writable('/tmp') ? 'YES' : 'NO')."\n";
    exit;
}

// 1. Siapkan environment default jika belum diatur di Vercel Dashboard
$envDefaults = [
    'APP_NAME' => 'Kayla Scrapbook',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'true',
    'APP_KEY' => 'base64:17UeYJ9XJRIvHuDZFyj0AhVLEwapOg4wT28tAMd/2ng=',
    'APP_URL' => 'https://'.($_SERVER['HTTP_HOST'] ?? 'localhost'),
    'LOG_CHANNEL' => 'stderr',
    'LOG_LEVEL' => 'debug',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'DB_PORT' => '4000',
    'DB_DATABASE' => 'web_scrap',
    'DB_USERNAME' => '2CWvHmcHm2JVKo8.root',
    'DB_PASSWORD' => 'wzb9P97tSjRYXtYL',
    'SESSION_DRIVER' => 'cookie',
    'SESSION_LIFETIME' => '120',
    'SESSION_ENCRYPT' => 'false',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'FILESYSTEM_DISK' => 'local',
];

// Pastikan DB_HOST tidak mengarah ke localhost/127.0.0.1 (karena tidak ada MySQL di container Lambda)
$currentDbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');
if (empty($currentDbHost) || in_array($currentDbHost, ['127.0.0.1', 'localhost', 'mysql'])) {
    $envDefaults['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
    putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
    $_ENV['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
    $_SERVER['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
}

$currentAppKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? '');
if (empty($currentAppKey)) {
    $fallbackKey = 'base64:17UeYJ9XJRIvHuDZFyj0AhVLEwapOg4wT28tAMd/2ng=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

foreach ($envDefaults as $key => $val) {
    if (empty($_ENV[$key]) && empty($_SERVER[$key]) && ! getenv($key)) {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

// 2. Siapkan folder penyimpanan dinamis di /tmp (read-only filesystem bypass di Lambda)
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

// Redirect cache dan log path Laravel ke /tmp dan stderr
$storageOverrides = [
    'APP_STORAGE' => '/tmp/storage',
    'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
    'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes-v7.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'LOG_CHANNEL' => 'stderr',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
];

foreach ($storageOverrides as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

try {
    define('LARAVEL_START', microtime(true));

    // 3. Load Composer Autoload
    $autoloadPath = __DIR__.'/../vendor/autoload.php';
    if (! file_exists($autoloadPath)) {
        throw new RuntimeException('vendor/autoload.php tidak ditemukan.');
    }
    require_once $autoloadPath;

    // 4. Load Laravel Application
    $appPath = __DIR__.'/../bootstrap/app.php';
    if (! file_exists($appPath)) {
        throw new RuntimeException('bootstrap/app.php tidak ditemukan.');
    }
    /** @var Application $app */
    $app = require_once $appPath;

    // Gunakan folder storage dinamis /tmp
    $app->useStoragePath('/tmp/storage');

    // 5. Handle HTTP Request
    $request = Request::capture();
    $app->handleRequest($request);

} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>500 Internal Server Error</title>';
    echo '<style>body{font-family:system-ui,-apple-system,sans-serif;padding:32px;background:#0f172a;color:#f8fafc;}h1{color:#ef4444;font-size:22px;}p{font-size:14px;color:#cbd5e1;}pre{background:#1e293b;padding:16px;border-radius:8px;overflow-x:auto;color:#38bdf8;font-size:13px;line-height:1.5;}</style></head><body>';
    echo '<h1>Server Error: '.htmlspecialchars($e->getMessage()).'</h1>';
    echo '<p><b>File:</b> '.htmlspecialchars($e->getFile()).' baris '.htmlspecialchars((string) $e->getLine()).'</p>';
    echo '<h3>Stack Trace:</h3>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
    echo '</body></html>';
}
