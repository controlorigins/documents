<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>Path Debug Test</h1>";

// Test different path formats that might be passed to the processor
$testPaths = [
    'Overview.md',
    'assets/markdown/Overview.md',
    'ChatGPT/1_ChatGPT Overview.md',
    'assets/markdown/ChatGPT/1_ChatGPT Overview.md'
];

$processor = new MarkdownProcessor();

foreach ($testPaths as $testPath) {
    echo "<h2>Testing: " . htmlspecialchars($testPath) . "</h2>";
    
    try {
        $result = $processor->convertFile($testPath);
        echo "<div style='color: green;'>✅ SUCCESS</div>";
        echo "<div>Preview: " . htmlspecialchars(substr(strip_tags($result), 0, 100)) . "...</div>";
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "<hr>";
}

// Also test what files actually exist
echo "<h2>File System Check</h2>";
$markdownDir = __DIR__ . '/assets/markdown/';
echo "<p>Markdown directory: " . htmlspecialchars($markdownDir) . "</p>";
echo "<p>Directory exists: " . (is_dir($markdownDir) ? 'Yes' : 'No') . "</p>";

if (is_dir($markdownDir)) {
    echo "<p>Contents:</p><ul>";
    foreach (scandir($markdownDir) as $item) {
        if ($item !== '.' && $item !== '..') {
            $fullPath = $markdownDir . $item;
            $type = is_dir($fullPath) ? 'DIR' : 'FILE';
            echo "<li>$type: " . htmlspecialchars($item) . "</li>";
        }
    }
    echo "</ul>";
}
?>
