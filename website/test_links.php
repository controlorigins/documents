<?php
require_once 'includes/docs.php';

echo "=== TESTING ALL 'HOW THIS PAGE WAS CREATED' LINKS ===\n";

$links = [
    'document_view.php' => 'ChatGPT/Sessions/Markdown PHP File Viewer',
    'search.php' => 'ChatGPT/Sessions/Search Markdown Files PHP',
    'project_list.php' => 'ChatGPT/Sessions/Create PHP Project Table',
    'joke.php' => 'ChatGPT/Sessions/Create PHP Joke Page', 
    'github.php' => 'ChatGPT/Sessions/GitHub API Access PHP',
    'database.php' => 'ChatGPT/Sessions/PHP Database CRUD',
    'data-analysis.php' => 'ChatGPT/Sessions/List CSV Files PHP'
];

foreach ($links as $phpFile => $markdownPath) {
    echo "\n$phpFile:\n";
    echo "  Expected file: $markdownPath.md\n";
    
    // Test doc_pretty_url
    $prettyUrl = doc_pretty_url($markdownPath);
    echo "  Pretty URL: $prettyUrl\n";
    
    // Test reverse lookup
    $slugPath = str_replace('/doc/', '', $prettyUrl);
    $foundFile = doc_lookup_file_by_slug($slugPath);
    echo "  Lookup result: " . ($foundFile ?? 'FILE NOT FOUND') . "\n";
    
    // Check if physical file exists
    $physicalPath = __DIR__ . '/assets/markdown/' . $markdownPath . '.md';
    echo "  Physical file exists: " . (file_exists($physicalPath) ? 'YES' : 'NO') . "\n";
    
    if ($foundFile && file_exists(__DIR__ . '/assets/markdown/' . $foundFile)) {
        echo "  STATUS: ✓ WORKING\n";
    } else {
        echo "  STATUS: ✗ BROKEN\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total links tested: " . count($links) . "\n";

// Test a few specific URL patterns that were problematic
echo "\n=== TESTING URL PATTERNS ===\n";
$testUrls = [
    '/doc/chatgpt/sessions/markdown-php-file-viewer',
    '/doc/ChatGPT/Sessions/Search%20Markdown%20Files%20PHP',
    '/doc/chatgpt%2Fsessions%2Fcreate-php-joke-page'
];

foreach ($testUrls as $url) {
    $slugPath = str_replace('/doc/', '', $url);
    $foundFile = doc_lookup_file_by_slug($slugPath);
    echo "URL: $url\n";
    echo "  Slug: $slugPath\n";
    echo "  Found: " . ($foundFile ?? 'NONE') . "\n\n";
}
?>
