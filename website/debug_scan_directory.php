<?php
// Function to recursively scan a directory and return an array of file paths
function scanDirectory($dir)
{
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

// Get all Markdown files and folder structure
$markdownFiles = scanDirectory('assets/markdown');

echo "<h1>Debug: scanDirectory Result</h1>";
echo "<pre>";
print_r($markdownFiles);
echo "</pre>";

echo "<h2>SEO-related files found:</h2>";
function findSeoFiles($items, $path = '') {
    foreach ($items as $item) {
        if (is_array($item)) {
            echo "Folder: " . $path . $item['folder'] . "/\n";
            findSeoFiles($item['subFiles'], $path . $item['folder'] . '/');
        } else {
            $fullPath = $path . $item;
            if (stripos($item, 'seo') !== false) {
                echo "SEO FILE FOUND: $fullPath\n";
            }
        }
    }
}

echo "<pre>";
findSeoFiles($markdownFiles);
echo "</pre>";
?>
