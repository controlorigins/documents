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

// Parse the selected Markdown file if one is specified
$markdownContent = '';
$selectedFile = '';
if (isset($_GET['file']) || (isset($markdownFiles[0]) && !isset($_GET['file']))) {
    $selectedFile = $_GET['file'] ?? $markdownFiles[0]; // Use the first file as default
    $requestedFile = 'assets/markdown/' . urldecode($selectedFile);
    if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
        // Include the Parsedown library
        require_once('pages/Parsedown.php');
        
        $markdownContent = file_get_contents($requestedFile);
        // Create a new Parsedown instance
        $parsedown = new Parsedown();
        // Parse Markdown to HTML
        $htmlContent = $parsedown->text($markdownContent);
    }
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Document Viewer</h2>
            <p class="text-light mb-0">Explore markdown documentation files</p>
        </div>
        <div>
            <a href="/?file=ChatGPT%252FSessions%252FMarkdown%2BPHP%2BFile%2BViewer.md" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/controlorigins/documents/blob/main/website/pages/document_view.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- Document selector with improved UI -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Document List</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET" id="docForm">
                            <div class="mb-3">
                                <label for="fileSelect" class="form-label">
                                    <i class="bi bi-file-earmark me-1"></i> Select a document:
                                </label>
                                <select class="form-select" name="file" id="fileSelect">
                                    <?php renderOptions($markdownFiles); ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-eye me-1"></i> View Document
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Document content with improved styling -->
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <?php echo pathinfo(urldecode($selectedFile), PATHINFO_FILENAME); ?>
                        </h5>
                    </div>
                    <div class="card-body document-display bg-light p-4" style="min-height: 400px; overflow-y: auto;">
                        <?php 
                        if (!empty($htmlContent)) {
                            echo $htmlContent;
                        } else {
                            echo '<div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> Please select a document to view its contents.
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-files me-2"></i><span id="documentCount" class="badge bg-primary rounded-pill"><?php echo count($markdownFiles); ?></span> documents available
            </div>
            <div>
                <a href="/?page=search" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search me-1"></i> Search Documents
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to automatically load the document when the select changes
    const fileSelect = document.getElementById('fileSelect');
    fileSelect.addEventListener('change', () => {
        document.getElementById('docForm').submit();
    });

    // Populate the selected value on page load
    window.addEventListener('DOMContentLoaded', () => {
        const currentFile = '<?php echo $selectedFile; ?>';
        fileSelect.value = currentFile;
    });
</script>