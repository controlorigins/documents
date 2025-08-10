<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

// Test with the exact content from your "Create PHP Joke Page.md" 
$processor = new MarkdownProcessor([
    'cache' => ['enabled' => false],
    'parser' => [
        'safeMode' => false,
        'markupEscaped' => false,
        'urlsLinked' => true
    ],
    'metadata' => ['enabled' => false],
    'debug' => false
]);

// Simulate loading the actual file content
$filePath = 'assets/markdown/ChatGPT/Sessions/Create PHP Joke Page.md';
$htmlOutput = $processor->convertFile($filePath);

// Extract just the first PHP code block for analysis
$pattern = '/<pre[^>]*class="[^"]*language-php[^"]*"[^>]*><code[^>]*class="[^"]*language-php[^"]*"[^>]*>.*?<\/code><\/pre>/s';
preg_match($pattern, $htmlOutput, $matches);

if ($matches) {
    echo "<h1>✅ PrismJS Enhancement Verified in Real Document!</h1>\n";
    echo "<h2>Sample Enhanced PHP Code Block:</h2>\n";
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb;'>\n";
    echo $matches[0];
    echo "</div>\n";
    echo "<h2>Raw HTML Structure:</h2>\n";
    echo "<pre style='background: #f8f9fa; padding: 10px; font-size: 12px;'>";
    echo htmlspecialchars($matches[0]);
    echo "</pre>\n";
} else {
    echo "<h1>❌ No enhanced code blocks found</h1>\n";
}

// Also check if both classes are present
$hasPreClass = strpos($htmlOutput, '<pre class="language-php">') !== false;
$hasCodeClass = strpos($htmlOutput, '<code class="language-php">') !== false;

echo "<h2>Class Verification:</h2>\n";
echo "<div style='background: #e7f3ff; padding: 10px; border: 1px solid #bee5eb;'>\n";
echo "• PRE elements with language-php class: " . ($hasPreClass ? "✅ Found" : "❌ Missing") . "<br>\n";
echo "• CODE elements with language-php class: " . ($hasCodeClass ? "✅ Found" : "❌ Missing") . "<br>\n";
if ($hasPreClass && $hasCodeClass) {
    echo "• <strong style='color: green;'>🎉 PrismJS optimization successful!</strong>\n";
}
echo "</div>\n";
?>
