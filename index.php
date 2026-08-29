<?php

/**
 * Laravel Shared Hosting Root Entry Point
 */

define('LARAVEL_START', microtime(true));

// Determine if the application is under maintenance...
if (file_exists(__DIR__.'/storage/framework/maintenance.php')) {
    require __DIR__.'/storage/framework/maintenance.php';
}

// Register the Auto Loader...
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
}

// Bootstrap Laravel and handle the request...
if (file_exists(__DIR__.'/bootstrap/app.php')) {
    (require_once __DIR__.'/bootstrap/app.php')
        ->handleRequest(Illuminate\Http\Request::capture());
}
