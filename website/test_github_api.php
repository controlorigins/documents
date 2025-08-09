<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing GitHub API...\n";

function testApiCall($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ControlOriginsDocuments');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "URL: $url\n";
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "cURL Error: $error\n";
    }
    
    if ($response) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "JSON Response: Valid\n";
            if (isset($data['message'])) {
                echo "API Message: " . $data['message'] . "\n";
            }
            if (isset($data['full_name'])) {
                echo "Repository: " . $data['full_name'] . "\n";
            }
        } else {
            echo "JSON Error: " . json_last_error_msg() . "\n";
            echo "Raw Response (first 200 chars): " . substr($response, 0, 200) . "\n";
        }
    } else {
        echo "No response received\n";
    }
    echo "---\n";
    
    return $response;
}

// Test the main repository endpoint
testApiCall('https://api.github.com/repos/controlorigins/documents');

// Test commits endpoint
testApiCall('https://api.github.com/repos/controlorigins/documents/commits?per_page=2');
?>
