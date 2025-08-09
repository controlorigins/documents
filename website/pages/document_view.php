<?php
require_once __DIR__ . '/../includes/docs.php';

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

// Function to render collapsible tree structure for documents
function renderDocumentTree($items, $basePath = '', $selectedFile = '', $level = 0)
{
    foreach ($items as $index => $item) {
        if (is_array($item)) {
            $folderId = 'folder-' . md5($basePath . $item['folder']);
            $hasSubfolders = false;
            foreach ($item['subFiles'] as $subItem) {
                if (is_array($subItem)) {
                    $hasSubfolders = true;
                    break;
                }
            }
            
            echo '<div class="folder-item mb-2">';
            echo '<div class="d-flex align-items-center">';
            echo '<button class="btn btn-link text-start p-0 me-2 folder-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#' . $folderId . '" aria-expanded="false">';
            echo '<i class="bi bi-chevron-right rotate-icon"></i>';
            echo '</button>';
            echo '<i class="bi bi-folder text-warning me-2"></i>';
            echo '<strong class="text-primary">' . htmlspecialchars($item['folder']) . '</strong>';
            echo '</div>';
            echo '<div class="collapse" id="' . $folderId . '">';
            echo '<div class="ms-4 mt-2">';
            renderDocumentTree($item['subFiles'], $basePath . $item['folder'] . '/', $selectedFile, $level + 1);
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            $relativePath = $basePath . $item;
            $filePathNoExt = preg_replace('/\.md$/i', '', $relativePath);
            $docUrl = doc_pretty_url($filePathNoExt);
            $displayValue = pathinfo($item, PATHINFO_FILENAME);
            $isSelected = ($selectedFile === $relativePath);
            
            $activeClass = $isSelected ? 'active bg-primary text-white' : '';
            $iconClass = $isSelected ? 'text-white' : 'text-info';
            
            echo '<div class="document-item mb-1">';
            echo '<a href="' . htmlspecialchars($docUrl) . '" class="btn btn-link text-start w-100 p-2 document-link ' . $activeClass . '" style="text-decoration: none;">';
            echo '<i class="bi bi-file-earmark-text me-2 ' . $iconClass . '"></i>';
            echo '<span>' . htmlspecialchars($displayValue) . '</span>';
            echo '</a>';
            echo '</div>';
        }
    }
}

// Parse the selected Markdown file if one is specified
$markdownContent = '';
$htmlContent = '';
$selectedFile = '';
if (isset($_GET['file'])) {
    // File specified via routing system
    $selectedFile = $_GET['file'];
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
} elseif (!empty($markdownFiles)) {
    // Default to first file only if no file specified and files exist
    $selectedFile = is_array($markdownFiles[0]) ? '' : $markdownFiles[0]; 
    if ($selectedFile) {
        $requestedFile = 'assets/markdown/' . urldecode($selectedFile);
        if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
            require_once('pages/Parsedown.php');
            $markdownContent = file_get_contents($requestedFile);
            $parsedown = new Parsedown();
            $htmlContent = $parsedown->text($markdownContent);
        }
    }
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>Document Viewer
                <!-- Permanent Toggle Navigator button -->
                <button type="button" class="btn btn-outline-primary btn-sm ms-3" id="toggleNavigatorHeader" title="Toggle Navigator">
                    <i class="bi bi-layout-sidebar me-1"></i> <span id="toggleNavigatorText">Hide Navigator</span>
                </button>
            </h2>
            <p class="text-light mb-0">Explore markdown documentation files</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Markdown PHP File Viewer.md'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/controlorigins/documents/blob/main/website/pages/document_view.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- Document navigator with collapsible tree -->
            <div class="col-md-4 mb-4" id="documentNavigator">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Document Navigator
                        </h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-light" id="expandAll" title="Expand All">
                                <i class="bi bi-arrows-expand"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light" id="collapseAll" title="Collapse All">
                                <i class="bi bi-arrows-collapse"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                        <?php renderDocumentTree($markdownFiles, '', $selectedFile); ?>
                    </div>
                </div>
            </div>
            
            <!-- Document content with improved styling -->
            <div class="col-md-8" id="documentContent">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <?php echo $selectedFile ? pathinfo(urldecode($selectedFile), PATHINFO_FILENAME) : 'Welcome'; ?>
                        </h5>
                    </div>
                    <div class="card-body document-display bg-light p-4" style="min-height: 400px; overflow-y: auto;">
                        <?php 
                        if (!empty($htmlContent)) {
                            echo $htmlContent;
                        } else {
                            echo '<div class="text-center text-muted p-5">';
                            echo '<i class="bi bi-file-earmark-text display-1 mb-3"></i>';
                            echo '<h4>Select a document to view</h4>';
                            echo '<p>Choose a document from the navigator on the left to get started.</p>';
                            echo '</div>';
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

<style>
/* Custom styles for the document navigator */
.folder-toggle {
    border: none !important;
    color: #0d6efd !important;
    font-size: 0.875rem;
    background: none !important;
}

.folder-toggle:hover {
    color: #0a58ca !important;
    background: none !important;
}

.folder-toggle .rotate-icon {
    transition: transform 0.3s ease;
}

.folder-toggle[aria-expanded="true"] .rotate-icon {
    transform: rotate(90deg);
}

.document-link {
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.document-link:not(.active):hover {
    background-color: #f8f9fa !important;
    color: #0d6efd !important;
}

.document-link.active {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.folder-item {
    border-left: 2px solid #e9ecef;
    padding-left: 0.5rem;
    margin-left: 0.5rem;
}

#documentNavigator {
    transition: all 0.3s ease;
}

#documentContent {
    transition: all 0.3s ease;
}

.navigator-hidden #documentNavigator {
    display: none;
}

.navigator-hidden #documentContent {
    width: 100%;
    max-width: 100%;
    flex: 0 0 100%;
}

.document-item {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Expand/Collapse All functionality
    const expandAllBtn = document.getElementById('expandAll');
    const collapseAllBtn = document.getElementById('collapseAll');
    const toggleNavigatorHeaderBtn = document.getElementById('toggleNavigatorHeader');
    const toggleNavigatorText = document.getElementById('toggleNavigatorText');
    const navigator = document.getElementById('documentNavigator');
    
    expandAllBtn?.addEventListener('click', function() {
        document.querySelectorAll('#documentNavigator .collapse').forEach(collapse => {
            if (bootstrap.Collapse.getInstance(collapse)) {
                bootstrap.Collapse.getInstance(collapse).show();
            } else {
                new bootstrap.Collapse(collapse, { show: true });
            }
        });
    });
    
    collapseAllBtn?.addEventListener('click', function() {
        document.querySelectorAll('#documentNavigator .collapse').forEach(collapse => {
            if (bootstrap.Collapse.getInstance(collapse)) {
                bootstrap.Collapse.getInstance(collapse).hide();
            } else {
                new bootstrap.Collapse(collapse, { hide: true });
            }
        });
    });
    
    // Permanent Toggle Navigator functionality
    toggleNavigatorHeaderBtn?.addEventListener('click', function() {
        if (document.body.classList.contains('navigator-hidden')) {
            // Show navigator
            document.body.classList.remove('navigator-hidden');
            toggleNavigatorText.textContent = 'Hide Navigator';
            this.title = 'Hide Navigator';
        } else {
            // Hide navigator
            document.body.classList.add('navigator-hidden');
            toggleNavigatorText.textContent = 'Show Navigator';
            this.title = 'Show Navigator';
        }
    });
    
    // Auto-expand path to selected document
    const activeDocument = document.querySelector('.document-link.active');
    if (activeDocument) {
        // Find all parent collapses and expand them
        let element = activeDocument;
        while (element) {
            const collapse = element.closest('.collapse');
            if (collapse) {
                if (bootstrap.Collapse.getInstance(collapse)) {
                    bootstrap.Collapse.getInstance(collapse).show();
                } else {
                    new bootstrap.Collapse(collapse, { show: true });
                }
            }
            element = collapse?.parentElement;
        }
    }
    
    // Handle folder toggle icon rotation
    document.querySelectorAll('.folder-toggle').forEach(toggle => {
        const targetId = toggle.getAttribute('data-bs-target');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            targetElement.addEventListener('shown.bs.collapse', function() {
                const icon = toggle.querySelector('.rotate-icon');
                if (icon) icon.style.transform = 'rotate(90deg)';
            });
            
            targetElement.addEventListener('hidden.bs.collapse', function() {
                const icon = toggle.querySelector('.rotate-icon');
                if (icon) icon.style.transform = 'rotate(0deg)';
            });
        }
    });
});
</script>