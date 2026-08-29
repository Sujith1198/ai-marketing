<?php

/**
 * Laravel Shared Hosting Entry Point & Diagnostic Guard
 */

define('LARAVEL_START', microtime(true));

// 1. Check Maintenance Mode
if (file_exists(__DIR__ . '/storage/framework/maintenance.php')) {
    require __DIR__ . '/storage/framework/maintenance.php';
}

// 2. Check Composer Autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    echo "<h1>500 Configuration Notice: Vendor Directory Missing</h1>";
    echo "<p>Please run <code>composer install --no-dev --optimize-autoloader</code> in your server terminal or upload the <code>vendor</code> folder to your server.</p>";
    exit;
}

require __DIR__ . '/vendor/autoload.php';

// 3. Check Application Bootstrap
if (!file_exists(__DIR__ . '/bootstrap/app.php')) {
    http_response_code(500);
    echo "<h1>500 Configuration Notice: Bootstrap File Missing</h1>";
    echo "<p>The <code>bootstrap/app.php</code> file is missing.</p>";
    exit;
}

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->handleRequest(Illuminate\Http\Request::capture());
