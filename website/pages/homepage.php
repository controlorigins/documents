<h1>Documentation</h1>
<div class="row">
    <div class="col-3">
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
            $files = [];
            $folders = [];
        
            // Separate files and folders
            foreach ($items as $item) {
                if (is_array($item)) {
                    $folders[] = $item;
                } else {
                    $files[] = $item;
                }
            }
        
            echo '<ul>';
            
            // Display files first
            foreach ($files as $file) {
                $relativePath = $basePath . $file;
                echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $file . '</a></li>';
            }
            
            // Then display folders
            foreach ($folders as $folder) {
                echo '<li><strong>' . $folder['folder'] . '</strong>';
                renderList($folder['subFiles'], $basePath . $folder['folder'] . '/');
                echo '</li>';
            }
        
            echo '</ul>';
        }
                // Display the nested list
        renderList($markdownFiles);
        ?>
    </ul>
    </div>
    <div class="col-9">
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
</div>
