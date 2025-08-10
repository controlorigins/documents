<?php
require_once __DIR__ . '/../includes/docs.php';
require_once __DIR__ . '/../includes/MarkdownProcessor.php';
// Define the root folder
$rootFolder = 'assets/markdown';
$searchTerm = '';
$matchingFiles = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search term from the user
    $searchTerm = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : '';
    
    if (!empty($searchTerm)) {
        // Function to recursively search for files containing the search term
        function searchFiles($folder, $searchTerm, $relativePath = '')
        {
            $results = [];
            $files = scandir($folder);
            
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $folder . '/' . $file;
                    $currentRelativePath = $relativePath !== '' ? $relativePath . '/' . $file : $file;
                    
                    if (is_dir($filePath)) {
                        // If it's a directory, recursively search it
                        $results = array_merge($results, searchFiles($filePath, $searchTerm, $currentRelativePath));
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                        // If it's a markdown file, check if it contains the search term
                        $content = file_get_contents($filePath);
                        
                        if (stripos($content, $searchTerm) !== false) {
                            // Get file metadata
                            $fileSize = filesize($filePath);
                            $lastModified = filemtime($filePath);
                            
                            // Extract title from first line of markdown
                            $lines = explode("\n", $content);
                            $title = trim(str_replace(['#', '*', '_'], '', $lines[0]));
                            
                            // Find the context of the search term (text surrounding the match)
                            $position = stripos($content, $searchTerm);
                            $start = max(0, $position - 50);
                            $length = strlen($searchTerm) + 100; // 50 chars before and after
                            $excerpt = substr($content, $start, $length);
                            
                            // Highlight the search term in the excerpt
                            $excerpt = preg_replace('/(' . preg_quote($searchTerm, '/') . ')/i', '<mark>$1</mark>', $excerpt);
                            
                            // Add ellipsis if needed
                            if ($start > 0) {
                                $excerpt = '...' . $excerpt;
                            }
                            if ($start + $length < strlen($content)) {
                                $excerpt .= '...';
                            }
                            
                            // Count the number of occurrences
                            $occurrences = substr_count(strtolower($content), strtolower($searchTerm));
                            
                            // Construct pretty /doc/ link (remove .md extension)
                            $prettyPath = doc_pretty_url($currentRelativePath);
                            $results[] = [
                                'link' => $prettyPath,
                                'text' => $file,
                                'title' => $title,
                                'excerpt' => $excerpt,
                                'size' => $fileSize,
                                'modified' => $lastModified,
                                'occurrences' => $occurrences,
                                'path' => $currentRelativePath
                            ];
                        }
                    }
                }
            }
            
            return $results;
        }
        
        // Search for files containing the search term
        $matchingFiles = searchFiles($rootFolder, $searchTerm);
        
        // Sort by relevance (number of occurrences)
        usort($matchingFiles, function($a, $b) {
            return $b['occurrences'] - $a['occurrences'];
        });
    }
}

// Function to format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-search me-2"></i>Document Search</h2>
            <p class="text-light mb-0">Find content across all documentation</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Search Markdown Files PHP'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/search.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-4">
                <!-- Search Form -->
                <div class="card border-primary">
                    <div class="card-body">
                        <form method="POST" class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label for="searchTerm" class="form-label">
                                    <i class="bi bi-search me-1"></i> Search Term
                                </label>
                                <input type="text" name="searchTerm" id="searchTerm" class="form-control form-control-lg" 
                                       placeholder="Enter keywords to search..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-1"></i> Search Documents
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Search Results -->
            <div class="col-md-12">
                <?php if (!empty($searchTerm)): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>
                                Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"
                            </h5>
                            <span class="badge bg-primary rounded-pill">
                                <?php echo count($matchingFiles); ?> results
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($matchingFiles)): ?>
                        <div class="list-group">
                            <?php foreach ($matchingFiles as $index => $file): ?>
                            <div class="list-group-item list-group-item-action search-result-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">
                                        <a href="<?php echo htmlspecialchars($file['link']); ?>" class="stretched-link">
                                            <?php echo htmlspecialchars($file['title'] ? $file['title'] : $file['text']); ?>
                                        </a>
                                    </h5>
                                    <span class="badge bg-info text-dark">
                                        <?php echo $file['occurrences']; ?> <?php echo $file['occurrences'] > 1 ? 'matches' : 'match'; ?>
                                    </span>
                                </div>
                                
                                <p class="text-success small mb-2">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    <?php echo htmlspecialchars($file['path']); ?>
                                </p>
                                
                                <div class="search-excerpt mb-2 p-2 bg-light rounded">
                                    <?php echo $file['excerpt']; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="bi bi-hdd me-1"></i> <?php echo formatFileSize($file['size']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> Last modified: <?php echo date('M j, Y', $file['modified']); ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i> No matching files found for "<?php echo htmlspecialchars($searchTerm); ?>".
                            <p class="mt-2 mb-0">Suggestions:</p>
                            <ul class="mb-0">
                                <li>Check your spelling</li>
                                <li>Try more general keywords</li>
                                <li>Try different keywords</li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> Please enter a search term.
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-search text-muted" style="font-size: 5rem;"></i>
                        <h3 class="mt-4 mb-3">Search Documentation</h3>
                        <p class="text-muted">
                            Enter keywords in the search box above to find relevant documents.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info mb-0">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-lightbulb fs-3"></i>
                        </div>
                        <div>
                            <h5>Search Tips</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Use specific keywords</strong></p>
                                    <p class="small text-muted">More specific terms yield better results</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Try multiple variations</strong></p>
                                    <p class="small text-muted">If you don't find what you need, try similar terms</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Combine related terms</strong></p>
                                    <p class="small text-muted">Search for multiple concepts to narrow results</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-result-item {
        position: relative;
        transition: transform 0.2s ease;
    }
    
    .search-result-item:hover {
        transform: translateX(5px);
    }
    
    .search-excerpt {
        font-size: 0.9rem;
        border-left: 3px solid #0d6efd;
    }
    
    mark {
        background-color: rgba(255, 193, 7, 0.5);
        padding: 0.1em 0.2em;
        border-radius: 2px;
    }
</style>