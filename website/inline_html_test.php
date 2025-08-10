<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>Inline HTML Test</h1>";

$processor = new MarkdownProcessor();

// Test cases for inline HTML
$testCases = [
    '<a id="top"></a>' => 'Anchor tag test',
    '# Header\n<a id="top"></a>\nSome text' => 'Anchor in content test',
    '<div class="container">Content</div>' => 'Div container test',
    '<img src="test.jpg" alt="Test">' => 'Image tag test',
    '<script>alert("xss")</script>' => 'XSS protection test (should be blocked)',
    '<p>Normal <strong>HTML</strong> content</p>' => 'Mixed HTML test'
];

foreach ($testCases as $markdown => $description) {
    echo "<h2>" . htmlspecialchars($description) . "</h2>";
    echo "<div><strong>Input:</strong> <code>" . htmlspecialchars($markdown) . "</code></div>";
    
    try {
        $result = $processor->convertString($markdown);
        echo "<div><strong>Output:</strong> " . $result . "</div>";
        echo "<div><strong>Raw HTML:</strong> <code>" . htmlspecialchars($result) . "</code></div>";
    } catch (Exception $e) {
        echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "<hr>";
}
?>
