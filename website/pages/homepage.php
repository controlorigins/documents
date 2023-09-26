<div class="row">
    <div class="col-12">
        <form action="" method="GET">
            <div class="form-group">
                <label for="fileSelect"><small>Select a file:</small></label>
                <select class="form-control" name="file" id="fileSelect">
                    <?php
                    // Function to recursively scan a directory and return an array of file paths
                    function scanDirectory($dir, $basePath = '')
                    {
                        $files = [];
                        $subFiles = [];

                        foreach (scandir($dir) as $file) {
                            if ($file !== '.' && $file !== '..') {
                                $filePath = $dir . '/' . $file;
                                $relativePath = $basePath . $file;
                                if (is_dir($filePath)) {
                                    $subFiles = array_merge($subFiles, scanDirectory($filePath, $relativePath . '/'));
                                } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                                    $files[] = $relativePath;
                                }
                            }
                        }

                        return array_merge($files, $subFiles);
                    }

                    // Get all Markdown files and folder structure
                    $markdownFiles = scanDirectory('assets/markdown');

                    // Function to render options for the dropdown
                    function renderDropdownOptions($items, $basePath = '')
                    {
                        $options = '';

                        foreach ($items as $item) {
                            if (is_array($item)) {
                                $folderName = $item['folder'];
                                $subOptions = renderDropdownOptions($item['subFiles'], $basePath . $folderName . '/');
                                if (!empty($subOptions)) {
                                    $options .= '<optgroup label="' . $folderName . '">' . $subOptions . '</optgroup>';
                                }
                            } else {
                                $relativePath = $basePath . $item;
                                $options .= '<option value="' . urlencode($relativePath) . '">' . $item . '</option>';
                            }
                        }

                        return $options;
                    }
                    // Render options for the dropdown
                    echo renderDropdownOptions($markdownFiles);
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
