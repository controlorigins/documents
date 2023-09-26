<?php
// Read the JSON content
$jsonContent = file_get_contents('content.json');
$content = json_decode($jsonContent, true);

// Check if a page is specified in the URL
$page = isset($_GET['page']) ? (string)$_GET['page'] : 'document_view';

// Check if the requested page exists in the content
if (!array_key_exists($page, $content) || !isset($content[$page]['title']) || !isset($content[$page]['content'])) {
    // Default to a 404 page if the page doesn't exist or lacks title/content
    $page = '404';
    $content[$page]['title'] = '404 - Not Found';
    $content[$page]['content'] = 'Page Not Found';
}

// Include the template file
include 'template.php';
?>

