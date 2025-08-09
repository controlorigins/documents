<?php
require_once 'includes/docs.php';

// Simulate the exact same conditions
$_GET['file'] = 'ChatGPT/Sessions/Create PHP Joke Page.md';

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

// Function to render options for the select dropdown using doc URLs  
function renderOptions($items, $basePath = '', $selectedFile = '')
{
    foreach ($items as $item) {
        if (is_array($item)) {
            echo '<optgroup label="' . $item['folder'] . '">';
            renderOptions($item['subFiles'], $basePath . $item['folder'] . '/', $selectedFile);
            echo '</optgroup>';
        } else {
            $relativePath = $basePath . $item;
            // Remove the .md extension for the file path
            $filePathNoExt = preg_replace('/\.md$/i', '', $relativePath);
            
            // Generate the proper doc URL for this file
            $docUrl = doc_pretty_url($filePathNoExt);
            
            // Remove the .md extension for display
            $displayValue = pathinfo($item, PATHINFO_FILENAME);
            
            // Check if this is the currently selected document
            // Compare the relative path (with .md) against the selected file
            $isSelected = ($selectedFile === $relativePath) ? ' selected' : '';
            
            echo '<option value="' . htmlspecialchars($docUrl) . '"' . $isSelected . '>' . htmlspecialchars($displayValue) . '</option>';
        }
    }
}

$markdownFiles = scanDirectory('assets/markdown');
$selectedFile = $_GET['file'];

echo "=== HTML OUTPUT TEST ===\n";
echo "Selected file: '$selectedFile'\n\n";
echo "Generated HTML:\n";
echo "<select>\n";
echo "<option value=\"\">-- Select Document --</option>\n";
renderOptions($markdownFiles, '', $selectedFile);
echo "</select>\n";
