<?php
require_once __DIR__ . '/../includes/docs.php';
$folderPath = 'data'; // Specify the folder path
$csvFiles = [];

// Check if the folder exists and get CSV files
if (is_dir($folderPath)) {
    // Open the directory
    if ($dirHandle = opendir($folderPath)) {
        // Loop through the files in the directory
        while (false !== ($file = readdir($dirHandle))) {
            // Check if the file has a ".csv" extension
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $csvFiles[] = $file;
            }
        }
        
        // Close the directory handle
        closedir($dirHandle);
        
        // Sort files alphabetically
        sort($csvFiles);
    }
}
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-table me-2"></i>CSV Data Analysis</h2>
            <p class="text-light mb-0">Analyze and explore your CSV files</p>
        </div>
        <div>
            <a href="/?page=chart" class="btn btn-primary btn-sm">
                <i class="bi bi-bar-chart me-1"></i> Create Charts
            </a>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/List CSV Files PHP'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/data-analysis.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- CSV File Selector -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV Files</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($csvFiles)): ?>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-files me-1"></i> Available CSV files:
                            </label>
                            
                            <?php foreach ($csvFiles as $file): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input csv-file-radio" type="radio" name="csvFile" id="<?php echo $file; ?>" value="<?php echo $file; ?>">
                                <label class="form-check-label" for="<?php echo $file; ?>">
                                    <i class="bi bi-file-earmark-text me-1"></i> <?php echo $file; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="loading-indicator" class="text-center d-none mb-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">Loading data...</span>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i> No CSV files found in the data folder.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Analysis Results -->
            <div class="col-md-8">
                <div id="analysis-results" class="d-none">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up me-2"></i>
                                Analysis of <span id="current-file-name"></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Field Summary -->
                            <h6 class="card-subtitle mb-3 text-muted">
                                <i class="bi bi-list-columns me-1"></i> Field Summary
                            </h6>
                            
                            <div class="table-responsive mb-4">
                                <table id="summary-table" class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Field</th>
                                            <th>Minimum</th>
                                            <th>Average</th>
                                            <th>Maximum</th>
                                            <th>Most Common</th>
                                            <th>Least Common</th>
                                            <th>Distinct Count</th>
                                        </tr>
                                    </thead>
                                    <tbody id="summary-tbody">
                                        <!-- Summary data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5><i class="bi bi-grid-3x3 me-2"></i>Rows</h5>
                                            <h2 id="total-rows">0</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5><i class="bi bi-columns me-2"></i>Columns</h5>
                                            <h2 id="total-columns">0</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5><i class="bi bi-cells me-2"></i>Cells</h5>
                                            <h2 id="total-cells">0</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions Row -->
                            <div class="d-flex justify-content-between mb-3">
                                <h6 class="card-subtitle text-muted">
                                    <i class="bi bi-table me-1"></i> Data Table
                                </h6>
                                <div>
                                    <a href="/?page=chart" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-bar-chart me-1"></i> Visualize Data
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table id="csv-data-table" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                    <thead class="table-dark" id="data-table-header">
                                        <!-- Headers will be loaded dynamically -->
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="no-selection" class="card h-100">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-file-earmark-spreadsheet text-muted" style="font-size: 5rem;"></i>
                        <h3 class="mt-4 mb-3">Select a CSV File</h3>
                        <p class="text-muted">
                            Choose a CSV file from the list to view its contents and analysis.
                        </p>
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
                    <h5>What's CSV?</h5>
                    <p class="mb-0">
                        CSV (Comma-Separated Values) is a simple file format used to store tabular data, such as a spreadsheet or database.
                        Each line of the file is a data record consisting of one or more fields, separated by commas.
                        This tool helps you analyze and explore your CSV data with ease.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>