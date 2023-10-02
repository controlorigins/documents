<?php
// Function to make GET request using cURL
function curlGet($url) {
    $ch = curl_init(); // initialize curl handle

    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 10s

    $result = curl_exec($ch); // run the whole process
    curl_close($ch); 
    
    return $result;
}

$api_url = "https://v2.jokeapi.dev/joke/Any?safe-mode";
$response = curlGet($api_url);
$joke_data = json_decode($response, true);

// Check if it's a single or two-part joke
if(isset($joke_data['joke'])) {
    echo "<p><strong>Joke:</strong> {$joke_data['joke']}</p>";
} else {
    echo "<p><strong>Setup:</strong> {$joke_data['setup']}</p>";
    echo "<p><strong>Punchline:</strong> {$joke_data['delivery']}</p>";
}
?>
