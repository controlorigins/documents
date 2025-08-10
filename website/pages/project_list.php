<?php
require_once __DIR__ . '/../includes/docs.php';

// Load projects data from remote URL
$projectsUrl = 'https://markhazleton.com/projects.json';
$localJsonFile = 'data/projects.json'; // Fallback local file
$projects = [];
$error = '';
$dataSource = ''; // Track data source for display

// Function to fetch projects from remote URL
function fetchProjectsFromUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL peer verification for local dev
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Disable SSL host verification for local dev
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHPDocSpark/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return ['error' => 'cURL Error: ' . $curlError, 'data' => false];
    }
    
    if ($httpCode !== 200) {
        return ['error' => 'HTTP Error: ' . $httpCode, 'data' => false];
    }
    
    return ['error' => '', 'data' => $response];
}

// Try to fetch from remote URL first
$remoteResult = fetchProjectsFromUrl($projectsUrl);

if ($remoteResult['data'] !== false) {
    $projects = json_decode($remoteResult['data'], true) ?? [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'JSON decode error: ' . json_last_error_msg();
        $projects = [];
        $dataSource = 'Remote (failed)';
    } else {
        $dataSource = 'Remote';
        // Convert relative image paths to absolute URLs for remote data
        $baseUrl = 'https://markhazleton.com/';
        foreach ($projects as &$project) {
            if (isset($project['image']) && !filter_var($project['image'], FILTER_VALIDATE_URL)) {
                $project['image'] = $baseUrl . ltrim($project['image'], '/');
            }
        }
        unset($project); // Break the reference
    }
} else {
    $error = $remoteResult['error'];
    
    // Fallback to local file if remote fetch fails
    if (file_exists($localJsonFile)) {
        $jsonData = file_get_contents($localJsonFile);
        $projects = json_decode($jsonData, true) ?? [];
        $error .= ' (Using local fallback data)';
        $dataSource = 'Local (fallback)';
    } else {
        $dataSource = 'None (no data available)';
    }
}

// Group projects by first letter for filtering
$groupedProjects = [];
$projectLetters = [];

foreach ($projects as $project) {
    $firstLetter = strtoupper(substr($project['p'], 0, 1));
    if (!in_array($firstLetter, $projectLetters)) {
        $projectLetters[] = $firstLetter;
    }
    
    if (!isset($groupedProjects[$firstLetter])) {
        $groupedProjects[$firstLetter] = [];
    }
    
    $groupedProjects[$firstLetter][] = $project;
}

// Sort letters alphabetically
sort($projectLetters);
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-kanban me-2"></i>Projects List</h2>
            <p class="text-light mb-0">Explore our project portfolio</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Create PHP Project Table'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/project_list.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (!empty($projects)): ?>
        <!-- Project Filters -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="bi bi-funnel me-2"></i>Filter Projects</h5>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="projectSearch" class="form-control" placeholder="Search projects...">
                </div>
            </div>
            
            <div class="btn-group flex-wrap" role="group">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">
                    All
                </button>
                <?php foreach ($projectLetters as $letter): ?>
                <button type="button" class="btn btn-outline-primary" data-filter="<?php echo $letter; ?>">
                    <?php echo $letter; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Projects Display -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4" id="projectsContainer">
            <?php foreach ($projects as $index => $project): ?>
            <div class="col project-item" data-letter="<?php echo strtoupper(substr($project['p'], 0, 1)); ?>">
                <div class="card h-100 project-card">
                    <div class="card-img-top project-image-container">
                        <img src="<?php echo $project['image']; ?>" class="img-fluid project-image" alt="<?php echo htmlspecialchars($project['p']); ?>">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($project['p']); ?>
                        </h5>
                        <p class="card-text">
                            <?php echo htmlspecialchars($project['d']); ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo $project['h']; ?>" class="btn btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Project
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination for larger datasets -->
        <nav aria-label="Project pagination">
            <ul class="pagination justify-content-center" id="projectPagination">
                <!-- Pagination will be generated by JavaScript -->
            </ul>
        </nav>
        
        <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i> 
            Unable to load projects data. 
            <?php if (!empty($error)): ?>
                <br><small class="text-muted">Error: <?php echo htmlspecialchars($error); ?></small>
            <?php endif; ?>
            Please try refreshing the page.
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-kanban me-1"></i> <span id="projectCount" class="badge bg-primary rounded-pill"><?php echo count($projects); ?></span> projects
            </div>
            <div>
                <a href="/?page=github" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-github me-1"></i> View GitHub Repository
                </a>
            </div>
        </div>
        <div class="mt-2 text-center">
            <small class="text-muted">
                <i class="bi bi-<?php echo ($dataSource === 'Remote') ? 'cloud-check' : (($dataSource === 'Local (fallback)') ? 'hdd' : 'exclamation-triangle'); ?> me-1"></i>
                Data source: 
                <span class="<?php echo ($dataSource === 'Remote') ? 'text-success' : (($dataSource === 'Local (fallback)') ? 'text-warning' : 'text-danger'); ?>">
                    <?php echo htmlspecialchars($dataSource); ?>
                </span>
                <?php if ($dataSource === 'Remote'): ?>
                    <span class="text-muted">- markhazleton.com/projects.json</span>
                <?php elseif ($dataSource === 'Local (fallback)'): ?>
                    <span class="text-muted">- data/projects.json</span>
                <?php endif; ?>
            </small>
        </div>
    </div>
</div>

<style>
    .project-card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    
    .project-image-container {
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .project-image {
        max-height: 100%;
        object-fit: cover;
        width: 100%;
    }
</style>