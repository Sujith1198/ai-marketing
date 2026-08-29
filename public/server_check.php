<?php
/**
 * Standalone Hostinger Production Diagnostic & Database Auto-Installer
 * Access at: https://ai-marketing.gridaisync.com/server_check.php
 */

header('Content-Type: text/html; charset=utf-8');

$baseDir = file_exists(__DIR__ . '/.env') ? __DIR__ : dirname(__DIR__);

// Load embedded SQL dump if available
$sqlContent = null;
if (file_exists(__DIR__ . '/database_dump.php')) {
    require_once __DIR__ . '/database_dump.php';
    if (function_exists('get_ai_marketing_sql_dump')) {
        $sqlContent = get_ai_marketing_sql_dump();
    }
} elseif (file_exists($baseDir . '/database_dump.php')) {
    require_once $baseDir . '/database_dump.php';
    if (function_exists('get_ai_marketing_sql_dump')) {
        $sqlContent = get_ai_marketing_sql_dump();
    }
}

if (!$sqlContent) {
    $dumpFilePaths = [
        $baseDir . '/database/ai_marketing_production_dump.sql',
        __DIR__ . '/database/ai_marketing_production_dump.sql',
        __DIR__ . '/ai_marketing_production_dump.sql',
    ];
    foreach ($dumpFilePaths as $p) {
        if (file_exists($p)) {
            $sqlContent = file_get_contents($p);
            break;
        }
    }
}

echo "<!DOCTYPE html><html><head><title>Server Diagnostic & DB Installer</title>";
echo "<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;} .card{background:#1e293b;border-radius:8px;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 4px 6px rgba(0,0,0,0.3);} .ok{color:#4ade80;} .error{color:#f87171;} code{background:#334155;padding:2px 6px;border-radius:4px;color:#cbd5e1;}</style></head><body>";
echo "<h2>AI Marketing Team - Server Diagnostic Tool</h2>";

// 1. Check PHP Version
echo "<div class='card'><h3>1. PHP Environment</h3>";
echo "PHP Version: <strong>" . PHP_VERSION . "</strong><br>";
if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo "<span class='ok'>✓ PHP version is compatible (8.2+)</span>";
} else {
    echo "<span class='error'>✗ PHP version 8.2+ required. Please upgrade PHP in Hostinger hPanel.</span>";
}
echo "</div>";

// 2. Check Files Existence
echo "<div class='card'><h3>2. File Structure & Vendor</h3>";
$envExists = file_exists($baseDir . '/.env');
$vendorExists = file_exists($baseDir . '/vendor/autoload.php');

echo ".env File: " . ($envExists ? "<span class='ok'>✓ Found</span>" : "<span class='error'>✗ Missing! (Run: cp .env.production .env)</span>") . "<br>";
echo "vendor/autoload.php: " . ($vendorExists ? "<span class='ok'>✓ Found</span>" : "<span class='error'>✗ Missing! (Run: composer install --no-dev)</span>") . "<br>";
echo "SQL Dump Data: " . ($sqlContent ? "<span class='ok'>✓ Ready (" . strlen($sqlContent) . " bytes)</span>" : "<span class='error'>✗ Missing SQL content</span>") . "<br>";
echo "</div>";

// 3. Test MySQL DB Connection with multiple hosts
echo "<div class='card'><h3>3. Database Connection Test</h3>";

$hostsToTest = ['127.0.0.1', 'localhost', '217.21.90.174'];
$dbName = 'u376492188_aimarketing';
$dbUser = 'u376492188_aimarketing';
$dbPass = 'Sujith@0911@9099';
$connectedHost = null;

foreach ($hostsToTest as $h) {
    try {
        $pdo = new PDO("mysql:host={$h};dbname={$dbName};port=3306", $dbUser, $dbPass, [
            PDO::ATTR_TIMEOUT => 3,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "<span class='ok'>✓ Successfully connected to MySQL on host: <code>{$h}</code></span><br>";
        $connectedHost = $h;
        break;
    } catch (Exception $e) {
        echo "<span class='error'>Failed host '{$h}': " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
}
echo "</div>";

// 4. Auto Migration Action Button
echo "<div class='card'><h3>4. Run Automatic Migration & Import Dump</h3>";

if (isset($_GET['action']) && $_GET['action'] === 'import_sql') {
    if ($connectedHost && $sqlContent) {
        try {
            $pdo = new PDO("mysql:host={$connectedHost};dbname={$dbName};port=3306", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $pdo->exec($sqlContent);
            echo "<h4 class='ok'>✓ SUCCESS: SQL Dump Imported Successfully into Hostinger Database!</h4>";
            echo "<p>All 40+ tables and pre-seeded CEO user created!</p>";
            echo "<a href='/login' style='background:#2563eb;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;display:inline-block;margin-top:10px;'>Go to CEO Sign In</a>";
        } catch (Exception $ex) {
            echo "<h4 class='error'>Import Failed: " . htmlspecialchars($ex->getMessage()) . "</h4>";
        }
    } else {
        echo "<span class='error'>Cannot import SQL dump. Database not connected or SQL dump missing.</span>";
    }
} else {
    echo "<p>Click the button below to import the complete 40+ tables and seed default CEO user into Hostinger MySQL:</p>";
    echo "<a href='server_check.php?action=import_sql' style='background:#16a34a;color:white;padding:12px 24px;text-decoration:none;font-weight:bold;border-radius:6px;display:inline-block;'>⚡ Import SQL Dump & Migrate Database Now</a>";
}
echo "</div>";

echo "</body></html>";
