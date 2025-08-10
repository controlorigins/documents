<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

// Test markdown with PHP code blocks
$testMarkdown = '# Code Block Test

Here is some inline code: `$variable = "value"`

Here is a PHP code block:

```php
<?php
function fetchJoke() {
    $api_url = "https://v2.jokeapi.dev/joke/Any?safe-mode";
    $response = curlGet($api_url);
    return json_decode($response, true);
}
?>
```

And here is a generic code block:

```
This is plain code
No syntax highlighting
```

And another language:

```javascript
function test() {
    console.log("Hello World");
}
```
';

$processor = new MarkdownProcessor([
    'cache' => ['enabled' => false], // Disable cache for testing
    'parser' => [
        'safeMode' => false,
        'markupEscaped' => false,
        'urlsLinked' => true
    ],
    'metadata' => ['enabled' => false],
    'debug' => true
]);

echo "<h1>Code Block Parsing Test</h1>\n";

// Test string conversion
echo "<h2>Markdown Input:</h2>\n";
echo "<pre>" . htmlspecialchars($testMarkdown) . "</pre>\n";

echo "<h2>HTML Output (rendered):</h2>\n";
$htmlOutput = $processor->convertString($testMarkdown);
echo "<div style='border: 2px solid #007bff; padding: 15px; background: #f8f9fa;'>\n";
echo $htmlOutput;
echo "</div>\n";

echo "<h2>HTML Source (escaped for viewing):</h2>\n";
echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6;'>";
echo htmlspecialchars($htmlOutput);
echo "</pre>\n";
?>
