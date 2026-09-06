<?php

header('Content-Type: text/plain');

echo "=== VERCEL ISOLATION TEST ===\n";
echo "1. PHP Version: " . PHP_VERSION . "\n";
echo "2. Vendor Autoload: ";

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "EXISTS\n";
    try {
        require __DIR__ . '/../vendor/autoload.php';
        echo "   -> Autoload loaded successfully!\n";
    } catch (\Throwable $e) {
        echo "   -> ERROR loading autoload: " . $e->getMessage() . "\n";
    }
} else {
    echo "NOT FOUND\n";
}

echo "3. Bootstrap App: ";
if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
    echo "EXISTS\n";
    try {
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        echo "   -> App instance loaded successfully: " . get_class($app) . "\n";
    } catch (\Throwable $e) {
        echo "   -> ERROR loading app: " . $e->getMessage() . "\n";
    }
} else {
    echo "NOT FOUND\n";
}

echo "4. Checking TiDB / PDO: ";
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: '4000';
    $db   = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    
    $dsn = "mysql:host={$host};port={$port};dbname={$db}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "CONNECTED TO TIDB SUCCESSFULLY!\n";
} catch (\Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}