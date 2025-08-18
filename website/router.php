<?php

declare(strict_types=1);

/**
 * Router for PHP applications - works for both local development and Azure App Service
 * Start locally with: php -S localhost:8001 router.php
 * For Azure: configure as startup file or use with URL rewriting
 */

// Log requests in Azure for debugging
if (isset($_SERVER['WEBSITE_SITE_NAME'])) {
    error_log("Router: " . ($_SERVER['REQUEST_METHOD'] ?? 'GET') . " " . ($_SERVER['REQUEST_URI'] ?? '/'));
}

$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$realFile = __DIR__ . $reqPath;

// Serve actual files directly (for local dev server)
if ($reqPath !== '/' && is_file($realFile)) {
    return false; // Let server serve the static file
}

// For directories with trailing slash, check for index files
if ($reqPath !== '/' && is_dir($realFile)) {
    $indexFiles = ['index.php', 'index.html', 'index.htm'];
    foreach ($indexFiles as $indexFile) {
        if (is_file($realFile . '/' . $indexFile)) {
            require $realFile . '/' . $indexFile;
            exit;
        }
    }
}

// Special handling for static asset directories
$staticDirs = ['assets', 'css', 'js', 'img', 'images', 'fonts', 'vendor', 'data'];
$pathSegments = explode('/', trim($reqPath, '/'));
if (!empty($pathSegments) && in_array($pathSegments[0], $staticDirs)) {
    // This is a static asset request
    if (is_file($realFile)) {
        return false; // Let server handle it
    } else {
        // File not found
        http_response_code(404);
        exit('File not found');
    }
}

// Route everything else through the main application
require __DIR__ . '/index.php';
?>
