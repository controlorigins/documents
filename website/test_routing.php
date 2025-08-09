<?php
require_once __DIR__ . '/includes/docs.php';

echo "=== TESTING ROUTING FOR /doc/build-2fbuild-system-documentation ===\n";

// Simulate the routing logic from index.php
$path = '/doc/build-2fbuild-system-documentation';
echo "1. Original path: $path\n";

$docPath = substr($path, 5); // Remove '/doc/'
echo "2. Doc path after removing /doc/: '$docPath'\n";

// Full URL decode loop (like in index.php)
$prevDocPath = '';
while ($prevDocPath !== $docPath) {
    $prevDocPath = $docPath;
    $docPath = urldecode($docPath);
}
echo "3. After full URL decode: '$docPath'\n";

// Look up file
$file = doc_lookup_file_by_slug($docPath);
echo "4. File lookup result: '$file'\n";

if ($file) {
    $_GET['file'] = $file;
    echo "5. Set \$_GET['file'] = '$file'\n";
    
    // Test what document_view.php would do
    $requestedFile = 'assets/markdown/' . urldecode($file);
    echo "6. Requested file path: '$requestedFile'\n";
    echo "7. File exists: " . (file_exists($requestedFile) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($requestedFile)) {
        $content = file_get_contents($requestedFile);
        echo "8. Content length: " . strlen($content) . " characters\n";
        echo "9. First 100 chars: " . substr($content, 0, 100) . "...\n";
    }
} else {
    echo "5. No file found for slug '$docPath'\n";
}

echo "\n=== TESTING CURRENT DOCUMENT_VIEW.PHP LOGIC ===\n";

// Simulate what document_view.php currently does
function scanDirectory($dir)
{
    $files = [];
    $subfolderFiles = [];
    if (!is_dir($dir)) {
        echo "Directory does not exist: $dir\n";
        return [];
    }
    
    foreach (scandir($dir) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                $subFiles = scanDirectory($filePath);
                if (!empty($subFiles)) {
                    $subfolderFiles[] = [
                        'folder' => $file,
                        'subFiles' => $subFiles,
                    ];
                }
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $files[] = $file;
            }
        }
    }
    return array_merge($files, $subfolderFiles);
}

$markdownFiles = scanDirectory('assets/markdown');
echo "10. First file in markdownFiles array: " . (empty($markdownFiles) ? 'EMPTY' : $markdownFiles[0]) . "\n";

// Test the file selection logic from document_view.php
$selectedFile = '';
$htmlContent = '';
if (isset($_GET['file'])) {
    echo "11. \$_GET['file'] is set, using: " . $_GET['file'] . "\n";
    $selectedFile = $_GET['file'];
    $requestedFile = 'assets/markdown/' . urldecode($selectedFile);
    echo "12. Processing file: $requestedFile\n";
    if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
        $content = file_get_contents($requestedFile);
        echo "13. Successfully loaded content, length: " . strlen($content) . "\n";
        $htmlContent = "Content loaded successfully";
    } else {
        echo "13. File does not exist or is not markdown\n";
    }
} elseif (!empty($markdownFiles)) {
    echo "11. No \$_GET['file'], defaulting to first file\n";
    $selectedFile = is_array($markdownFiles[0]) ? '' : $markdownFiles[0]; 
    echo "12. Default selected file: '$selectedFile'\n";
    if ($selectedFile) {
        $requestedFile = 'assets/markdown/' . urldecode($selectedFile);
        echo "13. Default file path: $requestedFile\n";
    }
}

echo "14. Final selectedFile: '$selectedFile'\n";
echo "15. HTML content set: " . ($htmlContent ? 'YES' : 'NO') . "\n";
