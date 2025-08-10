<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

// Test markdown with various code block types
$testMarkdown = '# PrismJS Code Block Enhancement Test

## PHP Code Block
```php
<?php
function fetchJoke() {
    $api_url = "https://v2.jokeapi.dev/joke/Any?safe-mode";
    return curlGet($api_url);
}
?>
```

## JavaScript Code Block
```javascript
function fetchJoke() {
    $.ajax({
        url: "fetch_joke.php",
        method: "GET",
        success: function(response) {
            $("#joke-container").html(response);
        }
    });
}
```

## Generic Code Block
```
This is plain code
No specific language
```

## Inline Code
Here is some inline code: `$variable = "test"`

## CSS Code Block
```css
.code-block {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
}
```
';

$processor = new MarkdownProcessor([
    'cache' => ['enabled' => false],
    'parser' => [
        'safeMode' => false,
        'markupEscaped' => false,
        'urlsLinked' => true
    ],
    'metadata' => ['enabled' => false],
    'debug' => true
]);

echo "<h1>PrismJS Code Block Enhancement Test</h1>\n";

// Test string conversion
$htmlOutput = $processor->convertString($testMarkdown);

echo "<h2>Enhanced HTML Output (Rendered):</h2>\n";
echo "<div style='border: 2px solid #28a745; padding: 15px; background: #f8f9fa;'>\n";
echo $htmlOutput;
echo "</div>\n";

echo "<h2>Raw HTML Source (Check for PrismJS Classes):</h2>\n";
echo "<pre style='background: #e9ecef; padding: 15px; border: 1px solid #ced4da; font-size: 12px; white-space: pre-wrap;'>";
echo htmlspecialchars($htmlOutput);
echo "</pre>\n";

// Highlight specific parts for verification
echo "<h2>Code Block Analysis:</h2>\n";
$matches = [];
if (preg_match_all('/<pre[^>]*class="[^"]*language-([^"]*)"[^>]*><code[^>]*class="[^"]*language-([^"]*)"[^>]*>/i', $htmlOutput, $matches, PREG_SET_ORDER)) {
    echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h3>✅ PrismJS Enhancement Results:</h3>";
    foreach ($matches as $i => $match) {
        $preLanguage = $match[1];
        $codeLanguage = $match[2];
        echo "<div><strong>Code Block " . ($i + 1) . ":</strong></div>";
        echo "<div>• PRE element language class: <code>language-{$preLanguage}</code></div>";
        echo "<div>• CODE element language class: <code>language-{$codeLanguage}</code></div>";
        if ($preLanguage === $codeLanguage) {
            echo "<div style='color: green;'>✅ Classes match perfectly!</div>";
        } else {
            echo "<div style='color: red;'>❌ Classes don't match</div>";
        }
        echo "<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h3>❌ No enhanced code blocks found</h3>";
    echo "</div>";
}
?>
