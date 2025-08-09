<?php
/**
 * Custom 404 Handler for Azure App Service
 * This handles routing when pretty URLs don't work with Nginx
 */

// Get the original request
$originalUri = $_SERVER['REQUEST_URI'] ?? '/';
$originalPath = parse_url($originalUri, PHP_URL_PATH);

// Log for debugging
error_log("404 Handler: Original request = $originalUri");

// Check if this looks like a /doc/ URL that should be routed to our app
if (preg_match('#^/doc(?:/.*)?$#i', $originalPath)) {
    // This is a document URL that should be handled by our application
    // Set up the environment as if this request came directly to index.php
    $_SERVER['REQUEST_URI'] = $originalUri;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    
    // Include our main application
    require_once __DIR__ . '/index.php';
    exit;
}

// For other 404 errors, show a proper error page
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - PHPDocSpark</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        h1 { color: #fff; margin-bottom: 20px; }
        p { font-size: 18px; margin-bottom: 30px; opacity: 0.9; }
        .back-link a { 
            color: #fff; 
            text-decoration: none; 
            background: rgba(255,255,255,0.2);
            padding: 12px 24px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .back-link a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        .debug-info {
            margin-top: 30px;
            text-align: left;
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404 - Page Not Found</h1>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <div class="back-link">
            <a href="/">← Back to Home</a>
        </div>
        
        <div class="debug-info">
            <strong>Request Info:</strong><br>
            Path: <?= htmlspecialchars($originalPath) ?><br>
            URI: <?= htmlspecialchars($originalUri) ?><br>
            Timestamp: <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>
</body>
</html>
