<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/docs.php';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// GitHub API integration functions
function fetchApiData(string $url, ?string $token = null): ?array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // Add authorization header if token is provided
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . $token));
    }
    
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHPDocSpark');
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_error($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check HTTP response code
    if ($httpCode !== 200) {
        error_log('HTTP Error: ' . $httpCode . ' for URL: ' . $url);
        return null;
    }
    
    $data = json_decode($response, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        return null;
    }
    
    return $data;
}

// Cache settings
$token = ""; // GitHub token if needed
$cacheFile = 'data/commits_cache.json';
$cacheTime = 60 * 60; // 1 hour
$repo = 'markhazleton/PHPDocSpark';

$debugMode = false; // Set to false in production

// Check if cache is valid
$cacheIsValid = file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime);
$freshDataNeeded = !$cacheIsValid;

if ($debugMode) {
    echo "<!-- Debug: Cache file exists: " . (file_exists($cacheFile) ? 'Yes' : 'No') . " -->\n";
    echo "<!-- Debug: Cache valid: " . ($cacheIsValid ? 'Yes' : 'No') . " -->\n";
    echo "<!-- Debug: Fresh data needed: " . ($freshDataNeeded ? 'Yes' : 'No') . " -->\n";
}

if ($cacheIsValid) {
    $cachedData = json_decode(file_get_contents($cacheFile), true);
    if ($debugMode) {
        echo "<!-- Debug: Using cached data -->\n";
    }
} else {
    if ($debugMode) {
        echo "<!-- Debug: Fetching fresh data from GitHub API -->\n";
    }
    
    // Fetch fresh data from GitHub API
    $repoInfo = fetchApiData("https://api.github.com/repos/{$repo}", $token);
    $commits = fetchApiData("https://api.github.com/repos/{$repo}/commits?per_page=5", $token);
    $contributors = fetchApiData("https://api.github.com/repos/{$repo}/contributors?per_page=10", $token);
    
    if ($debugMode) {
        echo "<!-- Debug: Repo info: " . ($repoInfo ? 'Success' : 'Failed') . " -->\n";
        echo "<!-- Debug: Commits: " . ($commits ? count($commits) . ' found' : 'Failed') . " -->\n";
        echo "<!-- Debug: Contributors: " . ($contributors ? count($contributors) . ' found' : 'Failed') . " -->\n";
    }
    
    // Fetch commit details (but only if commits were successfully retrieved)
    if ($commits && is_array($commits)) {
        foreach ($commits as &$commit) {
            if (isset($commit['url'])) {
                $commit['details'] = fetchApiData($commit['url'], $token);
            }
        }
    }
    
    // Cache the data
    $cachedData = [
        'repoInfo' => $repoInfo,
        'commits' => $commits,
        'contributors' => $contributors,
        'timestamp' => time()
    ];
    
    // Only cache if we have at least some data
    if ($repoInfo || $commits || $contributors) {
        file_put_contents($cacheFile, json_encode($cachedData));
        if ($debugMode) {
            echo "<!-- Debug: Data cached successfully -->\n";
        }
    } else {
        if ($debugMode) {
            echo "<!-- Debug: No data to cache - API calls may have failed -->\n";
        }
    }
}

// Helper function to format date
function formatDate(string $dateString): string {
    $date = new DateTime($dateString);
    return $date->format('F j, Y g:i A');
}

// Calculate cache statistics
$cacheAge = time() - ($cachedData['timestamp'] ?? filemtime($cacheFile));
$nextRefresh = $cacheTime - $cacheAge;
$cacheDate = date('F j, Y g:i A', ($cachedData['timestamp'] ?? filemtime($cacheFile)));

// Format cache time
function formatTime(int $seconds): string {
    return sprintf('%02d:%02d:%02d', 
        (int)floor($seconds / 3600), 
        (int)floor(($seconds / 60) % 60), 
        (int)($seconds % 60)
    );
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-github me-2"></i>GitHub Repository Integration</h2>
            <p class="text-light mb-0">Live data from our GitHub repository</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/GitHub API Access PHP'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/github.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- Repository Information -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-dark">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Repository Info</h5>
                    </div>
                    <?php if (isset($cachedData['repoInfo'])): ?>
                    <?php $repoInfo = $cachedData['repoInfo']; ?>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <a href="<?php echo $repoInfo['html_url']; ?>" target="_blank">
                                <div class="display-1 mb-2">
                                    <i class="bi bi-github"></i>
                                </div>
                                <h5><?php echo $repoInfo['full_name']; ?></h5>
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <p class="text-muted mb-1"><?php echo $repoInfo['description']; ?></p>
                            
                            <div class="d-flex justify-content-between mb-3 mt-4">
                                <div class="text-center">
                                    <div class="h5 mb-0"><?php echo $repoInfo['stargazers_count']; ?></div>
                                    <small class="text-muted"><i class="bi bi-star me-1"></i>Stars</small>
                                </div>
                                <div class="text-center">
                                    <div class="h5 mb-0"><?php echo $repoInfo['forks_count']; ?></div>
                                    <small class="text-muted"><i class="bi bi-diagram-2 me-1"></i>Forks</small>
                                </div>
                                <div class="text-center">
                                    <div class="h5 mb-0"><?php echo $repoInfo['watchers_count']; ?></div>
                                    <small class="text-muted"><i class="bi bi-eye me-1"></i>Watchers</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-code me-2"></i>Language</span>
                                <span class="badge bg-primary rounded-pill"><?php echo $repoInfo['language']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-exclamation-circle me-2"></i>Open Issues</span>
                                <span class="badge bg-warning rounded-pill"><?php echo $repoInfo['open_issues_count']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar3 me-2"></i>Created</span>
                                <span><?php echo formatDate($repoInfo['created_at']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-arrow-clockwise me-2"></i>Last Updated</span>
                                <span><?php echo formatDate($repoInfo['updated_at']); ?></span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo $repoInfo['html_url']; ?>" class="btn btn-primary" target="_blank">
                            <i class="bi bi-github me-1"></i> View on GitHub
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i> Unable to load repository information.
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Commits -->
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-git me-2"></i>Recent Commits</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (isset($cachedData['commits']) && !empty($cachedData['commits'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($cachedData['commits'] as $commit): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <a href="<?php echo $commit['html_url']; ?>" target="_blank">
                                            <?php echo htmlspecialchars(substr($commit['sha'], 0, 7)); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo formatDate($commit['commit']['author']['date']); ?>
                                    </small>
                                </div>
                                
                                <p class="mb-2">
                                    <strong><i class="bi bi-chat-quote me-1"></i> Commit message:</strong> 
                                    <?php echo htmlspecialchars($commit['commit']['message']); ?>
                                </p>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?php echo $commit['author']['avatar_url']; ?>" 
                                        alt="<?php echo $commit['commit']['author']['name']; ?>" 
                                        class="rounded-circle me-2" 
                                        width="24" height="24">
                                    <span>
                                        <strong><?php echo $commit['commit']['author']['name']; ?></strong>
                                    </span>
                                </div>
                                
                                <?php if (isset($commit['details']['files']) && is_array($commit['details']['files'])): ?>
                                <div class="mt-2">
                                    <p class="mb-1">
                                        <i class="bi bi-file-earmark-diff me-1"></i>
                                        <strong>Files Changed:</strong> 
                                        <span class="badge bg-info rounded-pill">
                                            <?php echo count($commit['details']['files']); ?>
                                        </span>
                                    </p>
                                    
                                    <div class="small files-list">
                                        <?php foreach (array_slice($commit['details']['files'], 0, 3) as $file): ?>
                                        <div class="file-item">
                                            <i class="bi bi-file-earmark-code me-1"></i>
                                            <a href="https://github.com/<?php echo $repo; ?>/blob/main/<?php echo $file['filename']; ?>" 
                                               target="_blank" class="text-truncate">
                                                <?php echo $file['filename']; ?>
                                            </a>
                                            <span class="file-changes ms-1">
                                                <?php if (isset($file['additions']) && $file['additions'] > 0): ?>
                                                <span class="text-success">+<?php echo $file['additions']; ?></span>
                                                <?php endif; ?>
                                                
                                                <?php if (isset($file['deletions']) && $file['deletions'] > 0): ?>
                                                <span class="text-danger">-<?php echo $file['deletions']; ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($commit['details']['files']) > 3): ?>
                                        <div class="text-center mt-1">
                                            <a href="<?php echo $commit['html_url']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                <?php echo count($commit['details']['files']) - 3; ?> more files
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning m-3">
                            <i class="bi bi-exclamation-triangle me-2"></i> No commits found or an error occurred.
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="https://github.com/<?php echo $repo; ?>/commits" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-clock-history me-1"></i> View All Commits
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contributors Section -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Top Contributors</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($cachedData['contributors']) && !empty($cachedData['contributors'])): ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4">
                            <?php foreach ($cachedData['contributors'] as $contributor): ?>
                            <div class="col">
                                <div class="card h-100 text-center contributor-card">
                                    <div class="card-body">
                                        <img src="<?php echo $contributor['avatar_url']; ?>" 
                                            alt="<?php echo $contributor['login']; ?>" 
                                            class="rounded-circle mb-3" 
                                            width="64" height="64">
                                        
                                        <h6 class="card-title"><?php echo $contributor['login']; ?></h6>
                                        
                                        <div class="mt-2">
                                            <span class="badge bg-success">
                                                <i class="bi bi-code-square me-1"></i>
                                                <?php echo number_format($contributor['contributions']); ?> commits
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer p-2">
                                        <a href="<?php echo $contributor['html_url']; ?>" class="btn btn-sm btn-primary w-100" target="_blank">
                                            <i class="bi bi-github me-1"></i> Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i> No contributors found or an error occurred.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock-history me-2 fs-4"></i>
                    <div>
                        <h6 class="mb-0">Cache Information</h6>
                        <ul class="list-unstyled small mb-0">
                            <li>Generated: <?php echo $cacheDate; ?></li>
                            <li>Age: <?php echo formatTime($cacheAge); ?> (hh:mm:ss)</li>
                            <li>Next refresh: <?php echo formatTime($nextRefresh); ?> (hh:mm:ss)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info mb-0">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle fs-4"></i>
                        </div>
                        <div>
                            <h6>About GitHub Integration</h6>
                            <p class="small mb-0">
                                This page demonstrates PHP's ability to integrate with external APIs. 
                                It fetches data from the GitHub API, caches it for performance, and 
                                displays it in a user-friendly format.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .contributor-card {
        transition: transform 0.3s ease;
    }
    
    .contributor-card:hover {
        transform: translateY(-5px);
    }
    
    .files-list {
        max-height: 100px;
        overflow-y: auto;
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 8px;
    }
    
    .file-item {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 2px 0;
    }
    
    .file-changes {
        font-size: 0.8rem;
    }
</style>