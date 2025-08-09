<?php
// Function to make GET request using cURL with proper error handling
function curlGet($url)
{
    $ch = curl_init(); // initialize curl handle

    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 10s
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable SSL verification for development
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // disable SSL host verification
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'); // set user agent

    $result = curl_exec($ch); // run the whole process
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    return $result;
}

$api_url = "https://v2.jokeapi.dev/joke/Any?safe-mode";
$response = curlGet($api_url);

// Check if cURL request was successful
if ($response === false) {
    echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle me-2'></i>Unable to connect to the joke API. Please check your internet connection.</div>";
    exit;
}

$joke_data = json_decode($response, true);

// Check if JSON decoding was successful and data exists
if ($joke_data === null || !isset($joke_data['type'])) {
    echo "<div class='alert alert-danger'><i class='bi bi-exclamation-circle me-2'></i>Failed to parse joke data. Please try again.</div>";
    exit;
}

// Check for API errors
if (isset($joke_data['error']) && $joke_data['error'] === true) {
    echo "<div class='alert alert-danger'><i class='bi bi-exclamation-circle me-2'></i>API Error: " . ($joke_data['message'] ?? 'Unknown error') . "</div>";
    exit;
}

// Check if it's a single or two-part joke
if ($joke_data['type'] == 'single') {
    echo "<p><strong>Joke:</strong> " . htmlspecialchars($joke_data['joke']) . "</p>";
} else if ($joke_data['type'] == 'twopart') {
    echo "<p><strong>Setup:</strong> " . htmlspecialchars($joke_data['setup']) . "</p>";
    echo "<p><strong>Punchline:</strong> " . htmlspecialchars($joke_data['delivery']) . "</p>";
} else {
    echo "<div class='alert alert-info'><i class='bi bi-info-circle me-2'></i>No joke was returned. Please try again.</div>";
}
?>
