<?php
// Test the fixed routing logic
require_once 'includes/docs.php';

echo "=== TESTING FIXED ROUTING ===\n";

$testUrl = 'ChatGPT%252F1_ChatGPT%2BOverview';
echo "Testing URL: $testUrl\n";

// Simulate the fixed logic
$tmp = $testUrl;
while (strpos($tmp, '%') !== false) {
    $prev = $tmp;
    $tmp = rawurldecode($tmp);
    if ($prev === $tmp) break;
    echo "Decoded to: $tmp\n";
}

$segments = explode('/', $tmp);
echo "Segments: " . implode(', ', $segments) . "\n";

$slugSegments = array_map('doc_slugify_segment', $segments);
echo "Slug segments: " . implode(', ', $slugSegments) . "\n";

$canonicalSlugPath = implode('/', $slugSegments);
echo "Canonical slug path: $canonicalSlugPath\n";

$fileRel = doc_lookup_file_by_slug($canonicalSlugPath);
echo "Found file: " . ($fileRel ?? 'NULL') . "\n";

if ($fileRel) {
    $noExt = preg_replace('/\.md$/i', '', $fileRel);
    echo "File without extension: $noExt\n";
    
    global $DOCS_FILE_TO_SLUG;
    $canonicalSlug = $DOCS_FILE_TO_SLUG[$noExt] ?? $canonicalSlugPath;
    echo "Canonical slug from map: $canonicalSlug\n";
    echo "Final URL would be: /doc/$canonicalSlug\n";
}
?>
