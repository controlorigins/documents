<?php
require_once 'includes/docs.php';

echo "=== DEBUGGING DOCUMENT_VIEW ISSUE ===\n";

// Simulate what happens when we visit /doc/chatgpt/1-chatgpt-overview
echo "1. Simulating /doc/chatgpt/1-chatgpt-overview:\n";
$_SERVER['REQUEST_URI'] = '/doc/chatgpt/1-chatgpt-overview';
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
echo "Request path: $requestPath\n";

if (preg_match('#^/doc(?:/(.*))?$#i', $requestPath, $m)) {
    $slugPath = $m[1] ?? '';
    echo "Slug path: '$slugPath'\n";
    
    if ($slugPath !== '') {
        // Normalize any legacy double-encoding
        $tmp = $slugPath; $i=0;
        while ($i<3 && preg_match('/%25/i',$tmp)) { $tmp = rawurldecode($tmp); $i++; }
        $slugPath = strtolower($tmp);
        echo "Normalized slug: '$slugPath'\n";
        
        $fileRel = doc_lookup_file_by_slug($slugPath);
        echo "Found file: " . ($fileRel ?? 'NULL') . "\n";
        
        if ($fileRel) {
            $_GET['file'] = $fileRel;
            echo "Set \$_GET['file'] = '$fileRel'\n";
            
            $candidatePath = __DIR__ . '/assets/markdown/' . $fileRel;
            echo "Full path: $candidatePath\n";
            echo "File exists: " . (is_file($candidatePath) ? 'YES' : 'NO') . "\n";
        }
    }
}

echo "\n2. What document_view.php sees:\n";
echo "\$_GET['file']: " . ($_GET['file'] ?? 'NOT SET') . "\n";

// Test the document_view logic
if (isset($_GET['file'])) {
    $selectedFile = $_GET['file'];
    $requestedFile = 'assets/markdown/' . urldecode($selectedFile);
    echo "Selected file: '$selectedFile'\n";
    echo "Requested file path: '$requestedFile'\n";
    echo "File exists: " . (file_exists($requestedFile) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($requestedFile)) {
        $content = file_get_contents($requestedFile);
        echo "Content length: " . strlen($content) . " characters\n";
        echo "First 100 chars: " . substr($content, 0, 100) . "...\n";
    }
}

echo "\n3. Testing scanDirectory function:\n";
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
echo "Found " . count($markdownFiles) . " markdown entries\n";
echo "First few: ";
for ($i = 0; $i < min(3, count($markdownFiles)); $i++) {
    if (is_array($markdownFiles[$i])) {
        echo "[folder: " . $markdownFiles[$i]['folder'] . "] ";
    } else {
        echo $markdownFiles[$i] . " ";
    }
}
echo "\n";

// Test what happens with no $_GET['file']
if (!isset($_GET['file']) && isset($markdownFiles[0])) {
    $defaultFile = $markdownFiles[0];
    if (is_array($defaultFile)) {
        echo "Default would be a folder, not a file!\n";
    } else {
        echo "Default file would be: '$defaultFile'\n";
    }
}
?>
