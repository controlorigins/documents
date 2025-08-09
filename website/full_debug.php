<?php
require_once 'includes/docs.php';

echo "=== FULL DROPDOWN SELECTION DEBUG ===\n";

// Simulate accessing /doc/chatgpt/sessions/create-php-joke-page
echo "1. Simulating routing for /doc/chatgpt/sessions/create-php-joke-page\n";
$path = '/doc/chatgpt/sessions/create-php-joke-page';
$docPath = substr($path, 5); // 'chatgpt/sessions/create-php-joke-page'
echo "   Doc path: '$docPath'\n";

$file = doc_lookup_file_by_slug($docPath);
echo "   File lookup result: '$file'\n";

if ($file) {
    $_GET['file'] = $file;
    echo "   Set \$_GET['file'] = '$file'\n";
}

echo "\n2. Testing document_view.php logic:\n";

// Copy the exact logic from document_view.php
function scanDirectory($dir) {
    $files = [];
    $subfolderFiles = [];
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

// Test the file selection logic
$selectedFile = '';
if (isset($_GET['file'])) {
    $selectedFile = $_GET['file'];
    echo "   Selected file from \$_GET: '$selectedFile'\n";
} elseif (!empty($markdownFiles)) {
    $selectedFile = is_array($markdownFiles[0]) ? '' : $markdownFiles[0]; 
    echo "   Using default first file: '$selectedFile'\n";
}

echo "   Final selectedFile: '$selectedFile'\n";

echo "\n3. Testing renderOptions logic:\n";

// Find the target file in the structure
function findFileInStructure($items, $basePath = '', $targetFile = '') {
    foreach ($items as $item) {
        if (is_array($item)) {
            echo "   Checking folder: {$item['folder']}\n";
            $found = findFileInStructure($item['subFiles'], $basePath . $item['folder'] . '/', $targetFile);
            if ($found) return $found;
        } else {
            $relativePath = $basePath . $item;
            echo "   Checking file: '$relativePath' vs target: '$targetFile'\n";
            if ($targetFile === $relativePath) {
                echo "   *** FOUND MATCH! ***\n";
                return $relativePath;
            }
        }
    }
    return false;
}

echo "   Looking for selectedFile '$selectedFile' in markdown structure:\n";
$foundMatch = findFileInStructure($markdownFiles, '', $selectedFile);
echo "   Match found: " . ($foundMatch ? 'YES' : 'NO') . "\n";

echo "\n4. Testing actual option generation:\n";

function debugRenderOptions($items, $basePath = '', $selectedFile = '', $depth = 0) {
    $indent = str_repeat('  ', $depth);
    foreach ($items as $item) {
        if (is_array($item)) {
            echo $indent . "OPTGROUP: {$item['folder']}\n";
            debugRenderOptions($item['subFiles'], $basePath . $item['folder'] . '/', $selectedFile, $depth + 1);
        } else {
            $relativePath = $basePath . $item;
            $filePathNoExt = preg_replace('/\.md$/i', '', $relativePath);
            $docUrl = doc_pretty_url($filePathNoExt);
            $displayValue = pathinfo($item, PATHINFO_FILENAME);
            $isSelected = ($selectedFile === $relativePath);
            
            echo $indent . "OPTION: '$displayValue'\n";
            echo $indent . "  File path: '$relativePath'\n";
            echo $indent . "  Selected file: '$selectedFile'\n";
            echo $indent . "  Match: " . ($isSelected ? 'YES' : 'NO') . "\n";
            echo $indent . "  URL: '$docUrl'\n";
            
            if ($isSelected) {
                echo $indent . "  *** THIS SHOULD BE SELECTED ***\n";
            }
        }
    }
}

echo "   Generating options with selectedFile = '$selectedFile':\n";
debugRenderOptions($markdownFiles, '', $selectedFile, 1);
