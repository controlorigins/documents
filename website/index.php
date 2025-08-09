<?php
// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple router
$page = $_GET['page'] ?? 'document_view'; // Default page
$validPages = [
    'document_view',
    'data-analysis',
    'chart',
    'project_list',
    'github',
    'crud',
    'joke',
    'search',
    '404'
];

// Check if the requested page is valid
if (!in_array($page, $validPages)) {
    $page = '404';
}

// Page titles
$pageTitles = [
    'document_view' => 'Document Viewer',
    'data-analysis' => 'CSV File Analysis',
    'chart' => 'Interactive Charting',
    'project_list' => 'Projects List',
    'github' => 'GitHub Repository Info',
    'crud' => 'Database CRUD',
    'joke' => 'Random Jokes',
    'search' => 'Search Documents',
    '404' => 'Page Not Found'
];

// Set the page title
$pageTitle = $pageTitles[$page] . ' | Control Origins Docs';

// Start output buffering to capture page content
ob_start();

// Include the requested page
include 'pages/' . $page . '.php';

// Get the buffered content
$pageContent = ob_get_clean();

// Include the layout template
include 'layout.php';
?>