<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <form method="get" action="">
        <label for="fileSelect">Select a Markdown File:</label>
        <select name="file" id="fileSelect">
            <option value="">Select a file...</option>
            <?php
            // Function to recursively scan a directory and return an array of file paths
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

            // Get all Markdown files and folder structure
            $markdownFiles = scanDirectory('assets/markdown');

            // Function to render options for the select dropdown
            function renderOptions($items, $basePath = '') {
                foreach ($items as $item) {
                    if (is_array($item)) {
                        echo '<optgroup label="' . $item['folder'] . '">';
                        renderOptions($item['subFiles'], $basePath . $item['folder'] . '/');
                        echo '</optgroup>';
                    } else {
                        $relativePath = $basePath . $item;
                        echo '<option value="' . urlencode($relativePath) . '">' . $item . '</option>';
                    }
                }
            }

            // Display options in the select dropdown
            renderOptions($markdownFiles);
            ?>
        </select>
        <button type="submit">Load File</button>
    </form>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            // Decode the file path to correct double encoding
            $requestedFile = urldecode($_GET['file']);
            $requestedFilePath = 'assets/markdown/' . $requestedFile;
            
            if (file_exists($requestedFilePath) && pathinfo($requestedFilePath, PATHINFO_EXTENSION) === 'md') {
                
                // Include the Parsedown library
                require_once('pages/Parsedown.php');

                $markdownContent = file_get_contents($requestedFilePath);
                // Create a new Parsedown instance
                $parsedown = new Parsedown();
                // Parse Markdown to HTML and display it
                echo $parsedown->text($markdownContent);
                
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
    <script>
        // JavaScript to submit the form when a file is selected
        document.getElementById('fileSelect').addEventListener('change', function() {
            document.getElementById('markdownForm').submit();
        });
    </script></body>
</html>
