<?php
require_once __DIR__ . '/../includes/MarkdownProcessor.php';

// Initialize processor
$processor = new MarkdownProcessor();

// Handle cache operations
$cacheMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'clear_cache':
            $pattern = isset($_POST['pattern']) && !empty($_POST['pattern']) ? $_POST['pattern'] : null;
            $result = $processor->clearCache($pattern);
            $cacheMessage = $result['message'];
            break;
    }
}

// Get system statistics
$stats = $processor->getStats();
$health = $processor->healthCheck();

// Determine overall status
$statusClass = [
    'healthy' => 'success',
    'warning' => 'warning', 
    'error' => 'danger'
][$health['status']] ?? 'secondary';

$statusIcon = [
    'healthy' => 'check-circle',
    'warning' => 'exclamation-triangle',
    'error' => 'x-circle'
][$health['status']] ?? 'question-circle';
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-cpu me-2"></i>Markdown Processor Status
            </h2>
            <p class="text-light mb-0">Unified markdown processing system monitoring</p>
        </div>
        <div>
            <span class="badge bg-<?php echo $statusClass; ?> fs-6">
                <i class="bi bi-<?php echo $statusIcon; ?> me-1"></i>
                <?php echo ucfirst($health['status']); ?>
            </span>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Health Status -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-<?php echo $statusClass; ?>">
                    <div class="card-header bg-<?php echo $statusClass; ?> text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-heart-pulse me-2"></i>System Health Check
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($health['issues'])): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle me-1"></i>Critical Issues</h6>
                            <ul class="mb-0">
                                <?php foreach ($health['issues'] as $issue): ?>
                                <li><?php echo htmlspecialchars($issue); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($health['warnings'])): ?>
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-circle me-1"></i>Warnings</h6>
                            <ul class="mb-0">
                                <?php foreach ($health['warnings'] as $warning): ?>
                                <li><?php echo htmlspecialchars($warning); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($health['recommendations'])): ?>
                        <div class="alert alert-info">
                            <h6><i class="bi bi-lightbulb me-1"></i>Recommendations</h6>
                            <ul class="mb-0">
                                <?php foreach ($health['recommendations'] as $rec): ?>
                                <li><?php echo htmlspecialchars($rec); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>Performance Metrics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-primary"><?php echo $stats['performance']['conversions_total']; ?></div>
                                    <small class="text-muted">Total Conversions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-success"><?php echo $stats['performance']['cache_hit_rate']; ?>%</div>
                                    <small class="text-muted">Cache Hit Rate</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 text-info"><?php echo $stats['performance']['average_processing_time_ms']; ?>ms</div>
                                    <small class="text-muted">Avg Processing Time</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 text-warning"><?php echo $stats['performance']['memory_usage_mb']; ?>MB</div>
                                    <small class="text-muted">Memory Usage</small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($stats['performance']['errors'] > 0): ?>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong><?php echo $stats['performance']['errors']; ?></strong> errors recorded
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-hdd me-2"></i>Cache Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($stats['cache']['enabled']): ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-primary"><?php echo $stats['cache']['total_files']; ?></div>
                                    <small class="text-muted">Cached Files</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-success"><?php echo $stats['cache']['total_size_formatted']; ?></div>
                                    <small class="text-muted">Cache Size</small>
                                </div>
                            </div>
                            <?php if (isset($stats['cache']['oldest_entry'])): ?>
                            <div class="col-12">
                                <small class="text-muted">
                                    <strong>Oldest:</strong> <?php echo date('M j, Y H:i', strtotime($stats['cache']['oldest_entry'])); ?><br>
                                    <strong>Newest:</strong> <?php echo date('M j, Y H:i', strtotime($stats['cache']['newest_entry'])); ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Cache Management -->
                        <div class="mt-3">
                            <?php if (!empty($cacheMessage)): ?>
                            <div class="alert alert-info alert-sm">
                                <?php echo htmlspecialchars($cacheMessage); ?>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="action" value="clear_cache">
                                <input type="text" name="pattern" class="form-control form-control-sm" 
                                       placeholder="Pattern (optional)" title="Leave empty to clear all cache">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i>Clear
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>Cache is disabled
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Version and Security Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Version Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Parsedown:</strong></td>
                                <td>
                                    <?php echo htmlspecialchars($stats['version']['parsedown_version']); ?>
                                    <?php if ($stats['version']['is_unknown_version']): ?>
                                    <span class="badge bg-danger ms-1">Unknown Version!</span>
                                    <?php elseif (!$stats['version']['is_stable_version']): ?>
                                    <span class="badge bg-warning ms-1">Pre-release</span>
                                    <?php else: ?>
                                    <span class="badge bg-success ms-1">Stable</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Processor:</strong></td>
                                <td><?php echo htmlspecialchars($stats['version']['processor_version']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>PHP:</strong></td>
                                <td><?php echo htmlspecialchars($stats['version']['php_version']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Composer:</strong></td>
                                <td>
                                    <?php echo $stats['version']['composer_managed'] ? 'Yes' : 'No'; ?>
                                    <?php if (!$stats['version']['composer_managed']): ?>
                                    <span class="badge bg-warning ms-1">Manual</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                        
                        <?php if (!empty($stats['version']['security_recommendations'])): ?>
                        <div class="alert alert-warning alert-sm">
                            <h6 class="alert-heading">Security Notes:</h6>
                            <ul class="small mb-0">
                                <?php foreach (array_slice($stats['version']['security_recommendations'], 0, 3) as $rec): ?>
                                <li><?php echo htmlspecialchars($rec); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check me-2"></i>Security Configuration
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">Safe Mode:</span>
                                    <?php if ($stats['security']['safe_mode']): ?>
                                    <span class="badge bg-success">Enabled</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Disabled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">HTML Escaping:</span>
                                    <?php if ($stats['security']['html_escaping']): ?>
                                    <span class="badge bg-success">Enabled</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Disabled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">URL Linking:</span>
                                    <?php if ($stats['security']['url_linking']): ?>
                                    <span class="badge bg-success">Enabled</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Disabled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">Strict Mode:</span>
                                    <?php if ($stats['security']['strict_mode']): ?>
                                    <span class="badge bg-info">Enabled</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Disabled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="small">
                            <div><strong>Max File Size:</strong> <?php echo $stats['security']['max_file_size']; ?></div>
                            <div><strong>Max Content Size:</strong> <?php echo $stats['security']['max_content_size']; ?></div>
                            <div class="mt-2">
                                <strong>Allowed Paths:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($stats['security']['allowed_paths'] as $path): ?>
                                    <li><code><?php echo htmlspecialchars($path); ?></code></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configuration Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>Current Configuration
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-<?php echo $stats['configuration']['cache_enabled'] ? 'success' : 'secondary'; ?>">
                                        <i class="bi bi-<?php echo $stats['configuration']['cache_enabled'] ? 'check' : 'x'; ?>-circle"></i>
                                    </div>
                                    <small>Cache Enabled</small>
                                    <?php if ($stats['configuration']['cache_enabled']): ?>
                                    <div class="small text-muted"><?php echo $stats['configuration']['cache_ttl_hours']; ?>h TTL</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-<?php echo $stats['configuration']['logging_enabled'] ? 'success' : 'secondary'; ?>">
                                        <i class="bi bi-<?php echo $stats['configuration']['logging_enabled'] ? 'check' : 'x'; ?>-circle"></i>
                                    </div>
                                    <small>Logging Enabled</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-<?php echo $stats['configuration']['metadata_enabled'] ? 'success' : 'secondary'; ?>">
                                        <i class="bi bi-<?php echo $stats['configuration']['metadata_enabled'] ? 'check' : 'x'; ?>-circle"></i>
                                    </div>
                                    <small>Metadata Enabled</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 text-<?php echo $stats['configuration']['debug_mode'] ? 'warning' : 'success'; ?>">
                                        <i class="bi bi-<?php echo $stats['configuration']['debug_mode'] ? 'exclamation-triangle' : 'shield-check'; ?>"></i>
                                    </div>
                                    <small>Debug Mode</small>
                                    <div class="small text-muted"><?php echo $stats['configuration']['debug_mode'] ? 'On' : 'Off'; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                <i class="bi bi-clock me-1"></i>Last updated: <?php echo date('M j, Y H:i:s'); ?>
            </div>
            <div>
                <a href="/?page=document_view" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-file-earmark-text me-1"></i>View Documents
                </a>
                <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.table-sm td {
    padding: 0.25rem;
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}
</style>
