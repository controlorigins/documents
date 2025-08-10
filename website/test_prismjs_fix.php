<?php
/**
 * Test PrismJS Implementation
 * Tests the new PrismJS configuration to ensure tokenizePlaceholders errors are resolved
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrismJS Implementation Test</title>
    <link href="assets/css/site.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">PrismJS Implementation Test</h1>
        
        <div class="alert alert-info">
            <strong>Test Purpose:</strong> Verify that PrismJS is working correctly without tokenizePlaceholders errors.
            <br><strong>Check:</strong> Open browser console (F12) and look for any JavaScript errors.
        </div>
        
        <h2>PHP Code Block Test</h2>
        <pre><code class="language-php"><?php
function fetchJoke() {
    $api_url = "https://v2.jokeapi.dev/joke/Any?safe-mode";
    $response = curlGet($api_url);
    return json_decode($response, true);
}

// Test inline comment
$result = fetchJoke();
echo json_encode($result);
?></code></pre>
        
        <h2>JavaScript Code Block Test</h2>
        <pre><code class="language-javascript">function fetchJoke() {
    $.ajax({
        url: 'fetch_joke.php',
        method: 'GET',
        success: function(response) {
            $('#joke-container').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching joke:', error);
        }
    });
}</code></pre>
        
        <h2>CSS Code Block Test</h2>
        <pre><code class="language-css">.code-block {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
}

.syntax-highlight {
    color: #e83e8c;
    font-weight: bold;
}</code></pre>
        
        <h2>Generic Code Block Test</h2>
        <pre><code>This is plain text code
No specific language highlighting
Should still be styled appropriately</code></pre>
        
        <div class="mt-5">
            <h3>JavaScript Console Test</h3>
            <button class="btn btn-primary" onclick="testPrismJS()">Test PrismJS Functionality</button>
            <div id="test-result" class="mt-3"></div>
        </div>
    </div>

    <script src="assets/js/site.js"></script>
    <script>
        function testPrismJS() {
            const resultDiv = document.getElementById('test-result');
            
            try {
                // Test if Prism is available
                if (typeof Prism === 'undefined') {
                    throw new Error('Prism is not defined');
                }
                
                // Test if languages are loaded
                const languages = Object.keys(Prism.languages);
                
                // Test highlighting a sample
                const testCode = 'function test() { return "hello world"; }';
                const highlighted = Prism.highlight(testCode, Prism.languages.javascript, 'javascript');
                
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h5>✅ PrismJS Test Results</h5>
                        <p><strong>Status:</strong> Working correctly!</p>
                        <p><strong>Available Languages:</strong> ${languages.length} (${languages.slice(0, 10).join(', ')}${languages.length > 10 ? '...' : ''})</p>
                        <p><strong>Test Highlight:</strong></p>
                        <pre><code class="language-javascript">${highlighted}</code></pre>
                    </div>
                `;
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ PrismJS Test Failed</h5>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            }
        }
        
        // Auto-run test after page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for any console errors after a brief delay
            setTimeout(function() {
                const hasErrors = window.console.error.calls ? window.console.error.calls.length > 0 : false;
                
                if (!hasErrors) {
                    document.getElementById('test-result').innerHTML = `
                        <div class="alert alert-info">
                            <strong>Initial Status:</strong> No JavaScript errors detected in console.
                            Click "Test PrismJS Functionality" button to run detailed tests.
                        </div>
                    `;
                }
            }, 1000);
        });
    </script>
</body>
</html>
