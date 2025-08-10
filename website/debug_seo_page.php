<?php
// Debug script to see what the SEO page is outputting
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/docs.php';

// Simulate the SEO page request
$_GET['page'] = 'document_view';
$_GET['file'] = 'Project/SEO.md';

// Capture output
ob_start();
include 'index.php';
$output = ob_get_clean();

// Display raw HTML with escaping for debugging
echo '<h1>Debug: SEO Page HTML Output</h1>';
echo '<textarea style="width:100%;height:800px;">' . htmlspecialchars($output) . '</textarea>';

echo '<hr>';
echo '<h2>Rendered Output:</h2>';
echo $output;
?>
