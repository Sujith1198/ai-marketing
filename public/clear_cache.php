<?php
/**
 * Standalone Laravel Cache Clearer
 * Access at: https://ai-marketing.gridaisync.com/clear_cache.php
 */

header('Content-Type: text/html; charset=utf-8');

$baseDir = file_exists(__DIR__ . '/bootstrap') ? __DIR__ : dirname(__DIR__);

$files = [
    $baseDir . '/bootstrap/cache/config.php',
    $baseDir . '/bootstrap/cache/routes-v7.php',
    $baseDir . '/bootstrap/cache/services.php',
    $baseDir . '/bootstrap/cache/packages.php',
];

$cleared = 0;
foreach ($files as $f) {
    if (file_exists($f)) {
        unlink($f);
        $cleared++;
    }
}

echo "<!DOCTYPE html><html><head><title>Cache Clearer</title>";
echo "<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;} .ok{color:#4ade80;} a{color:#3b82f6;}</style></head><body>";
echo "<h2 class='ok'>✓ Laravel Cache Cleared Successfully!</h2>";
echo "<p>Cleared {$cleared} bootstrap cache files.</p>";
echo "<p><a href='/login' style='background:#2563eb;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;display:inline-block;'>Go to CEO Sign In Page</a></p>";
echo "</body></html>";
