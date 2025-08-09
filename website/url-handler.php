<?php
/**
 * Azure App Service URL Handler
 * This file can be used as an alternative front controller
 */

// Check if this is a request for a pretty URL
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Log the request for debugging
if (function_exists('error_log')) {
    error_log("URL Handler: Processing request for: " . $requestUri);
}

// If this is a request for the handler itself, redirect to avoid loops
if (basename($_SERVER['SCRIPT_NAME']) === 'url-handler.php') {
    // Check if it's a pretty URL that needs processing
    if (preg_match('#^/doc(?:/(.*))?$#i', $path)) {
        // This is a doc URL, process it through index.php
        $_SERVER['REQUEST_URI'] = $requestUri;
        include __DIR__ . '/index.php';
        exit;
    }
}

// For all other cases, just include index.php
include __DIR__ . '/index.php';
?>
