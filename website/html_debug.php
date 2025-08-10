<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

echo "<h1>HTML Rendering Debug</h1>";

$processor = new MarkdownProcessor();

// Test 1: Simple HTML string
echo "<h2>Test 1: String Conversion</h2>";
$testMarkdown = '<a id="top"></a>';
$result = $processor->convertString($testMarkdown);
echo "<div><strong>Input:</strong> " . htmlspecialchars($testMarkdown) . "</div>";
echo "<div><strong>Output HTML:</strong> " . $result . "</div>";
echo "<div><strong>Raw Output:</strong> " . htmlspecialchars($result) . "</div>";

// Test 2: Check what's actually in the file
echo "<h2>Test 2: File Content Check</h2>";
$filePath = __DIR__ . '/assets/markdown/ChatGPT/1_ChatGPT Overview.md';
$rawContent = file_get_contents($filePath);
$firstFewLines = implode("\n", array_slice(explode("\n", $rawContent), 0, 5));
echo "<div><strong>Raw File Content (first 5 lines) - NOT ESCAPED:</strong></div>";
echo "<pre>" . $firstFewLines . "</pre>";

// Test 3: Process just the anchor line from the file
echo "<h2>Test 3: Process Anchor Line Only</h2>";
$lines = explode("\n", $rawContent);
$anchorLine = '';
foreach ($lines as $line) {
    if (strpos($line, '<a id="top"') !== false) {
        $anchorLine = $line;
        break;
    }
}
if ($anchorLine) {
    $anchorResult = $processor->convertString($anchorLine);
    echo "<div><strong>Anchor Line:</strong> " . htmlspecialchars($anchorLine) . "</div>";
    echo "<div><strong>Processed:</strong> " . $anchorResult . "</div>";
    echo "<div><strong>Raw:</strong> " . htmlspecialchars($anchorResult) . "</div>";
}

// Test 4: Full file processing
echo "<h2>Test 4: Full File Processing</h2>";
$fileResult = $processor->convertFile('assets/markdown/ChatGPT/1_ChatGPT Overview.md');
echo "<div><strong>First 200 chars of file result (NOT ESCAPED):</strong></div>";
echo "<div style='border: 1px solid #ccc; padding: 10px;'>" . substr($fileResult, 0, 200) . "</div>";
echo "<div><strong>First 200 chars of file result (ESCAPED for viewing):</strong></div>";
echo "<div style='border: 1px solid #ccc; padding: 10px;'>" . htmlspecialchars(substr($fileResult, 0, 200)) . "</div>";

// Check specifically for the anchor tag
if (strpos($fileResult, '<a id="top"></a>') !== false) {
    echo "<div style='color: green;'>✅ Found unescaped anchor tag in file result</div>";
} elseif (strpos($fileResult, '&lt;a id="top"&gt;&lt;/a&gt;') !== false) {
    echo "<div style='color: red;'>❌ Found escaped anchor tag in file result</div>";
} else {
    echo "<div style='color: orange;'>⚠️ Anchor tag not found in expected format</div>";
}
?>
