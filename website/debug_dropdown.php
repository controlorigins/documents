<?php
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

function renderOptionsDebug($items, $basePath = '', $depth = 0) {
    $indent = str_repeat('  ', $depth);
    foreach ($items as $item) {
        if (is_array($item)) {
            echo $indent . "OPTGROUP: {$item['folder']}\n";
            renderOptionsDebug($item['subFiles'], $basePath . $item['folder'] . '/', $depth + 1);
        } else {
            $relativePath = $basePath . $item;
            $displayValue = pathinfo($item, PATHINFO_FILENAME);
            $encodedPath = urlencode($relativePath);
            echo $indent . "OPTION: value='$encodedPath' display='$displayValue' (raw path: '$relativePath')\n";
        }
    }
}

echo "=== DROPDOWN OPTIONS ANALYSIS ===\n";
$markdownFiles = scanDirectory('assets/markdown');
echo "Raw markdown files structure:\n";
print_r($markdownFiles);

echo "\n=== DROPDOWN OPTIONS AS RENDERED ===\n";
renderOptionsDebug($markdownFiles);
