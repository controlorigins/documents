<?php
// Debug script to test docs mapping
require_once 'includes/docs.php';

echo "=== DOCS MAPPING DEBUG ===\n";
echo "DOCS_ROOT_DIR: " . $GLOBALS['DOCS_ROOT_DIR'] . "\n";
echo "Directory exists: " . (is_dir($GLOBALS['DOCS_ROOT_DIR']) ? 'YES' : 'NO') . "\n\n";

echo "FILE_TO_SLUG mappings:\n";
foreach ($GLOBALS['DOCS_FILE_TO_SLUG'] as $file => $slug) {
    echo "$file => $slug\n";
}

echo "\nSLUG_TO_FILE mappings:\n";
foreach ($GLOBALS['DOCS_SLUG_TO_FILE'] as $slug => $file) {
    echo "$slug => $file\n";
}

// Test specific cases
echo "\n=== TESTING SPECIFIC CASES ===\n";
$testFile = 'ChatGPT/1_ChatGPT Overview';
echo "Testing file: '$testFile'\n";
echo "doc_pretty_url result: " . doc_pretty_url($testFile) . "\n";

$testSlug = 'chatgpt/1-chatgpt-overview';
echo "Testing slug: '$testSlug'\n";
echo "doc_lookup_file_by_slug result: " . (doc_lookup_file_by_slug($testSlug) ?? 'NULL') . "\n";

// Test slugify function
echo "\n=== TESTING SLUGIFY ===\n";
$testSegments = ['ChatGPT', '1_ChatGPT Overview'];
foreach ($testSegments as $seg) {
    echo "'$seg' => '" . doc_slugify_segment($seg) . "'\n";
}
?>
