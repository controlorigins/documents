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
?></pre>
    </div>

    <div class="info">
        <h3>URL Rewriting Test</h3>
        <?php
        $htaccessPath = __DIR__ . '/.htaccess';
        $webConfigPath = __DIR__ . '/web.config';
        
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
        
        // Test if mod_rewrite is working by checking if we can access this file via a fake URL
        if (isset($_GET['test']) && $_GET['test'] === 'rewrite') {
            echo '<div class="success">URL Rewriting is working! ✓</div>';
        }
        ?>
        
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
