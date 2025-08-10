<?php
require_once __DIR__ . '/includes/docs.php';

echo "<h1>Debug: Document Mappings</h1>";

global $DOCS_FILE_TO_SLUG, $DOCS_SLUG_TO_FILE;

echo "<h2>File to Slug Mappings:</h2>";
echo "<pre>";
foreach ($DOCS_FILE_TO_SLUG as $file => $slug) {
    echo "File: $file => Slug: $slug\n";
}
echo "</pre>";

echo "<h2>Slug to File Mappings:</h2>";
echo "<pre>";
foreach ($DOCS_SLUG_TO_FILE as $slug => $file) {
    echo "Slug: $slug => File: $file\n";
}
echo "</pre>";

echo "<h2>SEO-related mappings:</h2>";
echo "<pre>";
foreach ($DOCS_FILE_TO_SLUG as $file => $slug) {
    if (stripos($file, 'seo') !== false || stripos($slug, 'seo') !== false) {
        echo "SEO File: $file => Slug: $slug\n";
    }
}
foreach ($DOCS_SLUG_TO_FILE as $slug => $file) {
    if (stripos($file, 'seo') !== false || stripos($slug, 'seo') !== false) {
        echo "SEO Slug: $slug => File: $file\n";
    }
}
echo "</pre>";

echo "<h2>Directory Structure Scan:</h2>";
function debugScanDirectory($dir, $level = 0) {
    $indent = str_repeat("  ", $level);
    echo $indent . "Dir: " . basename($dir) . "/\n";
    foreach (scandir($dir) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                debugScanDirectory($filePath, $level + 1);
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                echo $indent . "  File: $file\n";
            }
        }
    }
}

echo "<pre>";
debugScanDirectory('assets/markdown');
echo "</pre>";
?>
