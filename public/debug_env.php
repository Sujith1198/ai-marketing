<?php
/**
 * Standalone Laravel Error Interceptor & Debugger
 * Access at: https://ai-marketing.gridaisync.com/debug_env.php
 */

header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', '1');

$baseDir = file_exists(__DIR__ . '/bootstrap') ? __DIR__ : dirname(__DIR__);

// Auto-fix: Copy .env.production to .env if .env is missing or empty!
if (!file_exists($baseDir . '/.env') && file_exists($baseDir . '/.env.production')) {
    @copy($baseDir . '/.env.production', $baseDir . '/.env');
}

echo "<!DOCTYPE html><html><head><title>Laravel Exception Debugger</title>";
echo "<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;} .card{background:#1e293b;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 4px 6px rgba(0,0,0,0.3);} pre{background:#000;color:#00ff66;padding:1rem;overflow-x:auto;border-radius:6px;} .error{color:#f87171;} .ok{color:#4ade80;} code{background:#334155;padding:2px 6px;border-radius:4px;color:#cbd5e1;}</style></head><body>";
echo "<h2>Laravel Live Exception Diagnostics</h2>";

try {
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        throw new Exception("Vendor directory missing! Run: composer install");
    }
    require $baseDir . '/vendor/autoload.php';

    // Manually load Dotenv if env is empty
    if (class_exists('Dotenv\Dotenv') && file_exists($baseDir . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable($baseDir);
        $dotenv->safeLoad();
    }

    if (!file_exists($baseDir . '/bootstrap/app.php')) {
        throw new Exception("bootstrap/app.php missing!");
    }
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once $baseDir . '/bootstrap/app.php';

    $appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production';
    $dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'unknown';
    $dbName = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'unknown';

    echo "<div class='card'><h3 class='ok'>✓ Laravel Application Bootstrapped Cleanly</h3>";
    echo "<p>Environment: <code>" . htmlspecialchars($appEnv) . "</code></p>";
    echo "<p>Database Host: <code>" . htmlspecialchars($dbHost) . "</code> | Database Name: <code>" . htmlspecialchars($dbName) . "</code></p>";
    echo "</div>";

    echo "<div class='card'><h3>Attempting to Dispatch Request '/login'...</h3>";
    
    $request = Illuminate\Http\Request::create('/login', 'GET');
    $response = $app->handle($request);
    
    echo "<h4 class='ok'>✓ Dispatch Completed with HTTP Status: " . $response->getStatusCode() . "</h4>";
    if ($response->getStatusCode() >= 400) {
        echo "<h4>Response Payload Output:</h4>";
        echo "<pre>" . htmlspecialchars(substr($response->getContent(), 0, 5000)) . "</pre>";
    }
    echo "</div>";

} catch (Throwable $e) {
    echo "<div class='card'><h3 class='error'>✗ Exception Intercepted:</h3>";
    echo "<p><strong>Class:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
