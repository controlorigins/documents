<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>ChatGPT Document Test</h1>";

$processor = new MarkdownProcessor();

try {
    $result = $processor->convertFile('assets/markdown/ChatGPT/1_ChatGPT Overview.md');
    
    // Check if the anchor tag is properly rendered
    if (strpos($result, '<a id="top"></a>') !== false) {
        echo "<div style='color: green;'>✅ Anchor tag rendered correctly as HTML</div>";
    } else {
        echo "<div style='color: red;'>❌ Anchor tag not found or escaped</div>";
    }
    
    // Show first part of the document
    echo "<h2>Document Preview:</h2>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 400px; overflow: auto;'>";
    echo substr($result, 0, 1000) . "...";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
