<?php
// Azure deployment diagnostic page
// This file helps debug routing issues on Azure App Service
?>
<!DOCTYPE html>
<html>
<head>
    <title>Azure Deployment Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .error { background: #f8d7da; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Azure App Service - PHP Routing Diagnostics</h1>
    
    <div class="info">
        <h3>Request Information</h3>
        <pre><?php
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'Not set') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'Not set') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "\n";
?></pre>
    </div>

    <div class="info">
        <h3>Server Environment</h3>
        <pre><?php
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Not set') . "\n";
echo "PHP_VERSION: " . phpversion() . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Not set') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'Not set') . "\n";

// Check if running on Azure App Service
echo "\n--- Azure App Service Detection ---\n";
echo "WEBSITE_SITE_NAME: " . ($_SERVER['WEBSITE_SITE_NAME'] ?? 'Not set') . "\n";
echo "WEBSITE_INSTANCE_ID: " . ($_SERVER['WEBSITE_INSTANCE_ID'] ?? 'Not set') . "\n";
echo "WEBSITE_RESOURCE_GROUP: " . ($_SERVER['WEBSITE_RESOURCE_GROUP'] ?? 'Not set') . "\n";

// Check for Nginx vs Apache
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
if (stripos($serverSoftware, 'nginx') !== false) {
    echo "\n🔍 DETECTED: Nginx server (not Apache)\n";
    echo "❌ .htaccess files are ignored by Nginx\n";
    echo "✅ Need nginx.conf or App Service configuration\n";
} elseif (stripos($serverSoftware, 'apache') !== false) {
    echo "\n🔍 DETECTED: Apache server\n";
    echo "✅ .htaccess files should work\n";
} else {
    echo "\n🔍 Server type unclear from: $serverSoftware\n";
}
?></pre>
    </div>

    <div class="info">
        <h3>URL Rewriting Test</h3>
        <?php
        $htaccessPath = __DIR__ . '/.htaccess';
        $webConfigPath = __DIR__ . '/web.config';
        $routerPath = __DIR__ . '/router.php';
        
        if (file_exists($htaccessPath)) {
            echo '<div class="success">.htaccess file found ✓</div>';
        } else {
            echo '<div class="error">.htaccess file NOT found ✗</div>';
        }
        
        if (file_exists($webConfigPath)) {
            echo '<div class="success">web.config file found ✓</div>';
        } else {
            echo '<div class="warning">web.config file not found (OK for Linux)</div>';
        }
        
        if (file_exists($routerPath)) {
            echo '<div class="success">router.php file found ✓</div>';
        } else {
            echo '<div class="error">router.php file NOT found ✗</div>';
        }
        
        // Test if mod_rewrite is working by checking if we can access this file via a fake URL
        if (isset($_GET['test']) && $_GET['test'] === 'rewrite') {
            echo '<div class="success">URL Rewriting is working! ✓</div>';
        }
        
        // Show current routing method
        echo '<div class="info"><strong>Current Request Info:</strong><br>';
        echo 'REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'Not set') . '<br>';
        echo 'SCRIPT_NAME: ' . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . '<br>';
        echo 'PHP_SELF: ' . ($_SERVER['PHP_SELF'] ?? 'Not set') . '<br>';
        echo '</div>';
        ?>
        
        <p><strong>Test Links:</strong></p>
        <p><a href="/diagnostics.php?test=rewrite">Test URL Rewriting</a></p>
        <p><a href="/fake-url-that-should-redirect">Test Pretty URL Routing</a></p>
    </div>

    <div class="info">
        <h3>File System Check</h3>
        <pre><?php
$files = [
    'index.php',
    '.htaccess', 
    'web.config',
    'includes/config.php',
    'includes/docs.php',
    'assets/markdown'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ $file\n";
    } else {
        echo "✗ $file (missing)\n";
    }
}
?></pre>
    </div>

    <div class="info">
        <h3>PHP Configuration</h3>
        <pre><?php
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . error_reporting() . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
?></pre>
    </div>

    <div class="info">
        <h3>Test Your Pretty URLs</h3>
        <p>Try these URLs to test if routing is working:</p>
        <ul>
            <li><a href="/doc/chatgpt/sessions/create-php-joke-page">/doc/chatgpt/sessions/create-php-joke-page</a></li>
            <li><a href="/doc/test-document">/doc/test-document</a></li>
            <li><a href="/?page=joke">/?page=joke (fallback)</a></li>
        </ul>
    </div>
</body>
</html>
