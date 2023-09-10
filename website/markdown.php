<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <ul>
        <?php
        // Function to recursively scan a directory and return an array of file paths
        function scanDirectory($dir) {
            $files = [];
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    if (is_dir($filePath)) {
                        $subFiles = scanDirectory($filePath);
                        if (!empty($subFiles)) {
                            $files[] = [
                                'folder' => $file,
                                'subFiles' => $subFiles,
                            ];
                        }
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                        $files[] = $file;
                    }
                }
            }
            return $files;
        }

        // Get all Markdown files and folder structure
        $markdownFiles = scanDirectory('assets/markdown');

        // Function to render a nested list
        function renderList($items, $basePath = '') {
            echo '<ul>';
            foreach ($items as $item) {
                if (is_array($item)) {
                    echo '<li>' . $item['folder'];
                    renderList($item['subFiles'], $basePath . $item['folder'] . '/');
                    echo '</li>';
                } else {
                    $relativePath = $basePath . $item;
                    echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $item . '</a></li>';
                }
            }
            echo '</ul>';
        }

        // Display the nested list
        renderList($markdownFiles);
        ?>
    </ul>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            $requestedFile = 'assets/markdown/' . $_GET['file'];
            if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
                
                // Include the Parsedown library
                require_once('pages/Parsedown.php');

                $markdownContent = file_get_contents($requestedFile);
                // Create a new Parsedown instance
                $parsedown = new Parsedown();
                // Parse Markdown to HTML and display it
                echo $parsedown->text($markdownContent); // Corrected variable name
                
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
</body>
</html>
