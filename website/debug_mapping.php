<?php
require_once __DIR__ . '/includes/docs.php';

echo "=== EXAMINING DOCS MAPPING ===\n";

global $DOCS_FILE_TO_SLUG, $DOCS_SLUG_TO_FILE;

echo "Total files mapped: " . count($DOCS_SLUG_TO_FILE) . "\n\n";

echo "SLUG_TO_FILE mapping (first 20 entries):\n";
$count = 0;
foreach ($DOCS_SLUG_TO_FILE as $slug => $file) {
    echo "  '$slug' => '$file'\n";
    $count++;
    if ($count >= 20) break;
}

echo "\nLooking for 'build' related entries:\n";
foreach ($DOCS_SLUG_TO_FILE as $slug => $file) {
    if (stripos($slug, 'build') !== false || stripos($file, 'build') !== false) {
        echo "  '$slug' => '$file'\n";
    }
}

echo "\nTesting specific lookup:\n";
echo "Looking up 'build-2fbuild-system-documentation': ";
$result = doc_lookup_file_by_slug('build-2fbuild-system-documentation');
echo ($result ? "'$result'" : "NOT FOUND") . "\n";

echo "Looking up 'build/2-build-system-documentation': ";
$result = doc_lookup_file_by_slug('build/2-build-system-documentation');
echo ($result ? "'$result'" : "NOT FOUND") . "\n";

echo "Looking up 'build/build-system-documentation': ";
$result = doc_lookup_file_by_slug('build/build-system-documentation');
echo ($result ? "'$result'" : "NOT FOUND") . "\n";

// Check if the file exists in the filesystem
echo "\nChecking filesystem:\n";
$buildDir = __DIR__ . '/assets/markdown/Build';
if (is_dir($buildDir)) {
    echo "Build directory exists, contents:\n";
    foreach (scandir($buildDir) as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  $file\n";
        }
    }
} else {
    echo "Build directory does not exist\n";
}
