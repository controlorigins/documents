<?php
require_once 'includes/docs.php';

// Simulate routing - this would normally be done by index.php
$path = $_SERVER['REQUEST_URI'] ?? '/doc/chatgpt/sessions/create-php-joke-page';
if (strpos($path, '/doc/') === 0) {
    $docPath = substr($path, 5);
    $prevDocPath = '';
    while ($prevDocPath !== $docPath) {
        $prevDocPath = $docPath;
        $docPath = urldecode($docPath);
    }
    $file = doc_lookup_file_by_slug($docPath);
    if ($file) {
        $_GET['file'] = $file;
    }
}

// Same logic as document_view.php
function scanDirectory($dir) {
    $files = [];
    $subfolderFiles = [];
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

function renderOptions($items, $basePath = '', $selectedFile = '') {
    foreach ($items as $item) {
        if (is_array($item)) {
            echo '<optgroup label="' . $item['folder'] . '">';
            renderOptions($item['subFiles'], $basePath . $item['folder'] . '/', $selectedFile);
            echo '</optgroup>';
        } else {
            $relativePath = $basePath . $item;
            $filePathNoExt = preg_replace('/\.md$/i', '', $relativePath);
            $docUrl = doc_pretty_url($filePathNoExt);
            $displayValue = pathinfo($item, PATHINFO_FILENAME);
            $isSelected = ($selectedFile === $relativePath) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($docUrl) . '"' . $isSelected . '>' . htmlspecialchars($displayValue) . '</option>';
        }
    }
}

$markdownFiles = scanDirectory('assets/markdown');
$selectedFile = $_GET['file'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dropdown Test</title>
    <script>
        function debugDropdown() {
            const select = document.getElementById('fileSelect');
            const selectedIndex = select.selectedIndex;
            const selectedOption = select.options[selectedIndex];
            
            console.log('Selected index:', selectedIndex);
            console.log('Selected option text:', selectedOption.text);
            console.log('Selected option value:', selectedOption.value);
            console.log('Has selected attribute:', selectedOption.hasAttribute('selected'));
            
            // Check all options
            for (let i = 0; i < select.options.length; i++) {
                const option = select.options[i];
                if (option.hasAttribute('selected')) {
                    console.log('Found selected option:', option.text, 'at index', i);
                }
            }
        }
    </script>
</head>
<body>
    <h1>Dropdown Selection Test</h1>
    <p>Current file: <code><?= htmlspecialchars($selectedFile) ?></code></p>
    <p>Request URI: <code><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') ?></code></p>
    
    <select id="fileSelect" onchange="if(this.value) window.location.href = this.value">
        <option value="">-- Select Document --</option>
        <?php renderOptions($markdownFiles, '', $selectedFile); ?>
    </select>
    
    <br><br>
    <button onclick="debugDropdown()">Debug Dropdown</button>
    <p>Check browser console for debug output</p>
</body>
</html>
