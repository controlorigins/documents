<?php
require_once 'includes/docs.php';

// Test the exact URL that's failing
$failingUrl = '/doc/build-2fbuild-system-documentation';
echo "=== DEBUGGING FAILING URL ===\n";
echo "URL: $failingUrl\n";

// Simulate index.php routing logic
$docPath = substr($failingUrl, 5); // Remove '/doc/'
echo "Initial doc path: '$docPath'\n";

// Full URL decode loop
$prevDocPath = '';
while ($prevDocPath !== $docPath) {
    $prevDocPath = $docPath;
    $docPath = urldecode($docPath);
    echo "After decode: '$docPath'\n";
}

echo "Final doc path: '$docPath'\n";

// Try lookup
$file = doc_lookup_file_by_slug($docPath);
echo "File lookup result: " . ($file ?: 'NOT FOUND') . "\n";

// If not found, what would document_view.php do?
if (!$file) {
    echo "\nNo file found, document_view.php would default to first file\n";
    
    function scanDirectory($dir) {
        $files = [];
        $subfolderFiles = [];
        if (!is_dir($dir)) return [];
        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . '/' . $file;
                if (is_dir($filePath)) {
                    $subFiles = scanDirectory($filePath);
                    if (!empty($subFiles)) {
                        $subfolderFiles[] = ['folder' => $file, 'subFiles' => $subFiles];
                    }
                } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                    $files[] = $file;
                }
            }
        }
        return array_merge($files, $subfolderFiles);
    }
    
    $markdownFiles = scanDirectory('assets/markdown');
    echo "First markdown file: " . ($markdownFiles[0] ?? 'NONE') . "\n";
    echo "This explains why you see Overview.md content!\n";
}

echo "\n=== WHAT SHOULD HAPPEN ===\n";
$correctUrl = '/doc/build/build-system-documentation';
echo "Correct URL: $correctUrl\n";
$correctPath = substr($correctUrl, 5);
echo "Correct doc path: '$correctPath'\n";
$correctFile = doc_lookup_file_by_slug($correctPath);
echo "Correct file: " . ($correctFile ?: 'NOT FOUND') . "\n";

if ($correctFile) {
    echo "The correct URL works, so the issue is the malformed URL\n";
}
