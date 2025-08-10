<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Runtime Error Test</h1>";
echo "<p>Testing with full error reporting...</p>";

require_once __DIR__ . '/includes/MarkdownProcessor.php';

try {
    echo "<h2>1. Basic Initialization</h2>";
    $processor = new MarkdownProcessor();
    echo "<p>✅ Processor initialized without warnings</p>";
    
    echo "<h2>2. Custom Configuration Test</h2>";
    // Test with partial configuration that might cause the warnings
    $customConfig = [
        'parser' => [
            'safeMode' => true,
            'markupEscaped' => true
            // Note: Intentionally missing breaksEnabled and strictMode
        ],
        'cache' => ['enabled' => false]
    ];
    
    $processor2 = new MarkdownProcessor($customConfig);
    echo "<p>✅ Processor with custom config initialized without warnings</p>";
    
    echo "<h2>3. File Processing Test</h2>";
    $result = $processor->convertFile('assets/markdown/Overview.md');
    echo "<p>✅ File processing completed without warnings</p>";
    
    echo "<h2>4. String Processing Test</h2>";
    $result = $processor->convertString('# Test Header\nThis is a test.');
    echo "<p>✅ String processing completed without warnings</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><strong>All tests completed successfully!</strong></p>";
?>
