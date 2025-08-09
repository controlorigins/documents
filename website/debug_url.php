<?php
// Debug script to test URL decoding
echo "=== URL DECODING DEBUG ===\n";

$testUrl = 'ChatGPT%252F1_ChatGPT%2BOverview';
echo "Original URL: $testUrl\n";

// Simulate the current logic
$tmp = $testUrl; 
$i = 0;
echo "Decoding steps:\n";
while ($i < 3 && preg_match('/%25/i', $tmp)) { 
    $tmp = rawurldecode($tmp);
    $i++;
    echo "Step $i: $tmp\n";
}
$slugPath = strtolower($tmp);
echo "Final slugPath: '$slugPath'\n";

// Test different decoding approaches
echo "\n=== ALTERNATIVE APPROACHES ===\n";

// Approach 1: Full decode first
$fullDecoded = $testUrl;
while (strpos($fullDecoded, '%') !== false) {
    $prev = $fullDecoded;
    $fullDecoded = urldecode($fullDecoded);
    if ($prev === $fullDecoded) break; // prevent infinite loop
}
echo "Full decode: '$fullDecoded'\n";

// Approach 2: Handle spaces and slashes separately  
$approach2 = str_replace(['%252F', '%2B'], ['/', ' '], $testUrl);
$approach2 = urldecode($approach2);
echo "Approach 2: '$approach2'\n";

// What should the final slug be?
require_once 'includes/docs.php';
$expectedFile = 'ChatGPT/1_ChatGPT Overview';
$expectedSlug = doc_pretty_url($expectedFile);
echo "Expected slug from '$expectedFile': '$expectedSlug'\n";

// Test lookup
echo "\n=== LOOKUP TEST ===\n";
$testSlugs = [
    'chatgpt/1_chatgpt+overview',
    'chatgpt/1-chatgpt-overview',
    strtolower($fullDecoded),
    strtolower($approach2)
];

foreach ($testSlugs as $slug) {
    $result = doc_lookup_file_by_slug($slug);
    echo "Slug: '$slug' => " . ($result ?? 'NULL') . "\n";
}
?>
