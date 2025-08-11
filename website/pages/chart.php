<?php
require_once __DIR__ . '/../includes/docs.php';
$folderPath = 'data';
$csvFiles = [];
$selectedFile = $_GET['csv'] ?? '';

// Check if the folder exists and get CSV files
if (is_dir($folderPath)) {
    if ($dirHandle = opendir($folderPath)) {
        while (false !== ($file = readdir($dirHandle))) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $csvFiles[] = $file;
            }
        }
        closedir($dirHandle);
        sort($csvFiles);
    }
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Data Visualization</h2>
            <p class="text-light mb-0">Create interactive charts from your CSV data</p>
        </div>
        <div>
            <a href="/?page=data-analysis" class="btn btn-light btn-sm">
                <i class="bi bi-table me-1"></i> Back to Analysis
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/chart.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- Chart Configuration Panel -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Chart Configuration</h5>
                    </div>
                    <div class="card-body">
                        <!-- CSV File Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Select CSV File:
                            </label>
                            
                            <?php if (!empty($csvFiles)): ?>
                            <select class="form-select" id="csv-file-select">
                                <option value="">Choose a file...</option>
                                <?php foreach ($csvFiles as $file): ?>
                                <option value="<?php echo htmlspecialchars($file); ?>" <?php echo $selectedFile === $file ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($file); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i> No CSV files found.
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Chart Type Selection -->
                        <div class="mb-4" id="chart-config" style="display: <?php echo $selectedFile ? 'block' : 'none'; ?>;">
                            <label class="form-label fw-bold">
                                <i class="bi bi-graph-up me-1"></i> Chart Type:
                            </label>
                            <select class="form-select" id="chart-type">
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>
                        </div>
                        
                        <!-- Column Selection -->
                        <div class="mb-4" id="column-config" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag me-1"></i> Label Column:
                                </label>
                                <select class="form-select" id="label-column">
                                    <option value="">Select column...</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-123 me-1"></i> Data Column:
                                </label>
                                <select class="form-select" id="data-column">
                                    <option value="">Select column...</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="limit-config">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-funnel me-1"></i> Limit Results:
                                </label>
                                <select class="form-select" id="limit-rows">
                                    <option value="10">Top 10</option>
                                    <option value="20">Top 20</option>
                                    <option value="50">Top 50</option>
                                    <option value="0">All rows</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Generate Button -->
                        <div class="d-grid" id="generate-config" style="display: none;">
                            <button class="btn btn-success" id="generate-chart">
                                <i class="bi bi-play-circle me-1"></i> Generate Chart
                            </button>
                        </div>
                        
                        <!-- Loading indicator -->
                        <div id="chart-loading" class="text-center d-none">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2">Loading data...</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart Display Area -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Chart: <span id="chart-title">Select a file to begin</span>
                        </h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div id="no-chart" class="text-center">
                            <i class="bi bi-bar-chart text-muted" style="font-size: 5rem;"></i>
                            <h3 class="mt-4 mb-3">Create Your Visualization</h3>
                            <p class="text-muted">
                                Select a CSV file and configure your chart settings to generate an interactive visualization.
                            </p>
                        </div>
                        
                        <div id="chart-container" class="w-100" style="display: none;">
                            <canvas id="data-chart" width="800" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="alert alert-info mb-0">
            <div class="d-flex">
                <div class="me-3">
                    <i class="bi bi-lightbulb fs-3"></i>
                </div>
                <div>
                    <h5>Visualization Tips</h5>
                    <ul class="mb-0">
                        <li><strong>Bar Charts:</strong> Perfect for comparing categories or showing rankings</li>
                        <li><strong>Line Charts:</strong> Great for showing trends over time or continuous data</li>
                        <li><strong>Pie Charts:</strong> Ideal for showing proportions and percentages</li>
                        <li><strong>Interactive:</strong> Hover over chart elements for detailed information</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
