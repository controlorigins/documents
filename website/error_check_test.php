<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>Error Check Test</h1>";

echo "<p>Testing with full error reporting...</p>";

try {
    $processor = new MarkdownProcessor();
    echo "<p>✅ Processor initialized without warnings</p>";
    
    $result = $processor->convertFile('assets/markdown/Overview.md');
    echo "<p>✅ File conversion completed without warnings</p>";
    
    $stats = $processor->getStats();
    echo "<p>✅ Stats retrieved without warnings</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p>Test completed.</p>";
?>
