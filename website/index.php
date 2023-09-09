<?php
// Read the JSON content
$jsonContent = file_get_contents('content.json');
$content = json_decode($jsonContent, true);

// Check if a page is specified in the URL
$page = isset($_GET['page']) ? $_GET['page'] : 'homepage';

// Check if the requested page exists in the content
if (!array_key_exists($page, $content)) {
    $page = '404'; // Default to a 404 page if the page doesn't exist
}

// Include the specified PHP file if available
if (isset($content[$page]['php_file'])) {
    include $content[$page]['php_file'];
} else {
    // Include the template file if no PHP file is specified
    include 'template.php';
}
?>
