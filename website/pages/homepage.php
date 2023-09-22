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

        // Function to create a nested list of files and folders
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

            echo '<div class="accordion" id="file-list">';

            // Display folders first
            foreach ($folders as $folder) {
                echo '<div class="card">';
                echo '<div class="card-header" id="folder-' . $folder['folder'] . '">';
                echo '<h2 class="mb-0">';
                echo '<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#folder-' . $folder['folder'] . '-content" aria-expanded="true" aria-controls="folder-' . $folder['folder'] . '-content">';
                echo $folder['folder'];
                echo '</button>';
                echo '</h2>';
                echo '</div>';
                echo '<div id="folder-' . $folder['folder'] . '-content" class="collapse" aria-labelledby="folder-' . $folder['folder'] . '" data-parent="#file-list">';
                echo '<div class="card-body">';
                renderList($folder['subFiles'], $basePath . $folder['folder'] . '/');
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }

            // Then display files
            foreach ($files as $file) {
                $relativePath = $basePath . $file;
                echo '<div class="card">';
                echo '<div class="card-header" id="file-' . $file . '">';
                echo '<h2 class="mb-0">';
                echo '<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#file-' . $file . '-content" aria-expanded="true" aria-controls="file-' . $file . '-content">';
                echo $file;
                echo '</button>';
                echo '</h2>';
                echo '</div>';
                echo '<div id="file-' . $file . '-content" class="collapse" aria-labelledby="file-' . $file . '" data-parent="#file-list">';
                echo '<div class="card-body">';
                echo '<p>Content of ' . $file . ' goes here.</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }

            echo '</div>';
        }

        // Function to render a nested list
        function renderListOLD($items, $basePath = '') {
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
