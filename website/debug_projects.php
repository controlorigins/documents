<?php
// Debug script to test remote project fetching
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug: Project Data Fetching</h2>\n";

// Load projects data from remote URL
$projectsUrl = 'https://markhazleton.com/projects.json';
$localJsonFile = 'data/projects.json';
$projects = [];
$error = '';

// Function to fetch projects from remote URL
function fetchProjectsFromUrl($url) {
    echo "<p>Attempting to fetch from: <strong>$url</strong></p>\n";
    
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
    
    echo "<p>HTTP Code: <strong>$httpCode</strong></p>\n";
    if ($curlError) {
        echo "<p style='color: red;'>cURL Error: <strong>$curlError</strong></p>\n";
        return ['error' => 'cURL Error: ' . $curlError, 'data' => false];
    }
    
    if ($httpCode !== 200) {
        echo "<p style='color: red;'>HTTP Error: <strong>$httpCode</strong></p>\n";
        return ['error' => 'HTTP Error: ' . $httpCode, 'data' => false];
    }
    
    echo "<p style='color: green;'>Successfully fetched data from remote URL</p>\n";
    echo "<p>Response length: <strong>" . strlen($response) . "</strong> bytes</p>\n";
    
    return ['error' => '', 'data' => $response];
}

// Try to fetch from remote URL first
$remoteResult = fetchProjectsFromUrl($projectsUrl);

if ($remoteResult['data'] !== false) {
    echo "<h3>Remote Data Processing</h3>\n";
    $projects = json_decode($remoteResult['data'], true) ?? [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'JSON decode error: ' . json_last_error_msg();
        echo "<p style='color: red;'>JSON Error: <strong>$error</strong></p>\n";
        $projects = [];
    } else {
        echo "<p style='color: green;'>Successfully decoded JSON. Found <strong>" . count($projects) . "</strong> projects</p>\n";
        
        // Show original data for first project
        if (!empty($projects)) {
            echo "<h4>Original first project data:</h4>\n";
            echo "<pre>" . htmlspecialchars(json_encode($projects[0], JSON_PRETTY_PRINT)) . "</pre>\n";
        }
        
        // Convert relative image paths to absolute URLs for remote data
        $baseUrl = 'https://markhazleton.com/';
        echo "<p>Converting image paths using base URL: <strong>$baseUrl</strong></p>\n";
        
        foreach ($projects as $index => &$project) {
            if (isset($project['image'])) {
                $originalImage = $project['image'];
                $isUrl = filter_var($project['image'], FILTER_VALIDATE_URL);
                echo "<p>Project $index - Original image: <strong>$originalImage</strong> - Is URL: " . ($isUrl ? 'YES' : 'NO') . "</p>\n";
                
                if (!$isUrl) {
                    $project['image'] = $baseUrl . ltrim($project['image'], '/');
                    echo "<p>Project $index - Converted image: <strong>{$project['image']}</strong></p>\n";
                }
            }
        }
        unset($project); // Break the reference
        
        // Show converted data for first project
        if (!empty($projects)) {
            echo "<h4>Converted first project data:</h4>\n";
            echo "<pre>" . htmlspecialchars(json_encode($projects[0], JSON_PRETTY_PRINT)) . "</pre>\n";
        }
    }
} else {
    echo "<h3>Remote Fetch Failed</h3>\n";
    $error = $remoteResult['error'];
    echo "<p style='color: red;'>Error: <strong>$error</strong></p>\n";
    
    // Fallback to local file if remote fetch fails
    if (file_exists($localJsonFile)) {
        echo "<p>Falling back to local file: <strong>$localJsonFile</strong></p>\n";
        $jsonData = file_get_contents($localJsonFile);
        $projects = json_decode($jsonData, true) ?? [];
        $error .= ' (Using local fallback data)';
        echo "<p style='color: orange;'>Using local fallback data. Found <strong>" . count($projects) . "</strong> projects</p>\n";
    } else {
        echo "<p style='color: red;'>Local file not found: <strong>$localJsonFile</strong></p>\n";
    }
}

echo "<h3>Final Results</h3>\n";
echo "<p>Total projects loaded: <strong>" . count($projects) . "</strong></p>\n";
if (!empty($error)) {
    echo "<p style='color: red;'>Final error status: <strong>$error</strong></p>\n";
}
?>
