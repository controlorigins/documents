<?php
require_once __DIR__ . '/includes/docs.php';

echo "<h1>Debug: URL Generation for SEO</h1>";

// Test different SEO-related paths
$testPaths = [
    'SEO',
    'seo', 
    'Project/SEO',
    'project/seo',
    'Project/SEO.md'
];

foreach ($testPaths as $path) {
    echo "<h3>Testing path: '$path'</h3>";
    
    $prettyUrl = doc_pretty_url($path);
    echo "Pretty URL: $prettyUrl<br>";
    
    // Extract slug from pretty URL
    $slug = str_replace('/doc/', '', $prettyUrl);
    $lookupResult = doc_lookup_file_by_slug($slug);
    echo "Lookup result: " . ($lookupResult ?: 'NULL') . "<br>";
    
    echo "<hr>";
}

// Show the current mappings
global $DOCS_FILE_TO_SLUG, $DOCS_SLUG_TO_FILE;

echo "<h2>Current SEO-related mappings:</h2>";
foreach ($DOCS_FILE_TO_SLUG as $file => $slug) {
    if (stripos($file, 'seo') !== false || stripos($slug, 'seo') !== false) {
        echo "File: <strong>$file</strong> => Slug: <strong>$slug</strong><br>";
    }
}

foreach ($DOCS_SLUG_TO_FILE as $slug => $file) {
    if (stripos($file, 'seo') !== false || stripos($slug, 'seo') !== false) {
        echo "Slug: <strong>$slug</strong> => File: <strong>$file</strong><br>";
    }
}
?>
