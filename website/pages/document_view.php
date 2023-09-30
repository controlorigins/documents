<div class='card'>
    <div class='card-header'>
        <h1>Document Viewer</h1>  
        <a href='/?file=ChatGPT%252FSessions%252FMarkdown%2BPHP%2BFile%2BViewer.md'>
        How this page was created</a>
    </div>
    <div class="card-body">

<div class="row">
    <div class="col-12">
        <form action="" method="GET">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Document</label>
                </div>
                <select class="custom-select" name="file" id="fileSelect">
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

            // Function to render options for the select dropdown
            function renderOptions($items, $basePath = '')
            {
                foreach ($items as $item) {
                    if (is_array($item)) {
                        echo '<optgroup label="' . $item['folder'] . '">';
                        renderOptions($item['subFiles'], $basePath . $item['folder'] . '/');
                        echo '</optgroup>';
                    } else {
                        $relativePath = $basePath . $item;
                        // Remove the .md extension for display
                        $displayValue = pathinfo($item, PATHINFO_FILENAME);
                        echo '<option value="' . urlencode($relativePath) . '">' . $displayValue . '</option>';
                    }
                }
            }

            // Display options in the select dropdown
            renderOptions($markdownFiles);
            ?>
        </select>
            </div>
        </form>
        
        <div class="document-display">
            <?php
            // Display the selected Markdown file in HTML
            if (isset($_GET['file']) || (isset($markdownFiles[0]) && !isset($_GET['file']))) {
                $requestedFile = 'assets/markdown/' . urldecode($_GET['file'] ?? $markdownFiles[0]); // Decode the URL parameter or use the default
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
</div>


</div>
    <div class="card-footer">
        <br/>
        <br/>
    </div>
</div>
<br/><br/><br/><br/>



<script>
    // JavaScript to automatically load the document when the select changes
    const fileSelect = document.getElementById('fileSelect');
    fileSelect.addEventListener('change', () => {
        const selectedFile = fileSelect.value;
        // Redirect to the selected file
        window.location.href = `?file=${encodeURIComponent(selectedFile)}`;
    });

    // Populate the selected value on page load
    window.addEventListener('DOMContentLoaded', () => {
        const currentFile = '<?php echo isset($_GET['file']) ? $_GET['file'] : $markdownFiles[0]; ?>';
        fileSelect.value = currentFile;
    });
</script>
