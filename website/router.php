<?php
// Development router for PHP built-in server enabling pretty /doc/... URLs.
// Start with: php -S localhost:8001 router.php

$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$realFile = __DIR__ . $reqPath;
if ($reqPath !== '/' && is_file($realFile)) {
    return false; // Let server serve the static file
}
require __DIR__ . '/index.php';
