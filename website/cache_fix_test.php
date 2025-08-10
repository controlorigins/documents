<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>Cache Fix Test</h1>";

$processor = new MarkdownProcessor();

try {
    echo "<h2>First Load (no cache):</h2>";
    $result1 = $processor->convertFile('assets/markdown/ChatGPT/1_ChatGPT Overview.md');
    echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow: auto;'>";
    echo substr($result1, 0, 300) . "...";
    echo "</div>";
    
    echo "<h2>Second Load (from cache):</h2>";
    $result2 = $processor->convertFile('assets/markdown/ChatGPT/1_ChatGPT Overview.md');
    echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow: auto;'>";
    echo substr($result2, 0, 300) . "...";
    echo "</div>";
    
    echo "<h2>Results Match:</h2>";
    if ($result1 === $result2) {
        echo "<div style='color: green;'>✅ Cache working correctly - results identical</div>";
    } else {
        echo "<div style='color: red;'>❌ Cache issue - results different</div>";
    }
    
    // Check for serialized noise
    if (strpos($result2, 'a:5:{s:7:"content"') !== false) {
        echo "<div style='color: red;'>❌ Still showing serialized noise</div>";
    } else {
        echo "<div style='color: green;'>✅ No serialized noise found</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
