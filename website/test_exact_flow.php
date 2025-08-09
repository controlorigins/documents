<?php
require_once 'includes/docs.php';

echo "=== SIMULATING EXACT DROPDOWN BEHAVIOR ===\n";

// Simulate what the browser sends in POST
$_POST['selected_file'] = 'Build%2FBuild+System+Documentation.md';
echo "1. Raw POST data: " . $_POST['selected_file'] . "\n";

// PHP automatically URL-decodes POST data, so $_POST['selected_file'] is actually:
$actualPostData = urldecode('Build%2FBuild+System+Documentation.md');
echo "2. What PHP sees in \$_POST: '$actualPostData'\n";

// This is what document_view.php does:
$selectedRel = $actualPostData; // This is what $_POST['selected_file'] contains
echo "3. Selected file: '$selectedRel'\n";

$prettyUrl = doc_pretty_url($selectedRel);
echo "4. Pretty URL generated: '$prettyUrl'\n";

// Now test if this URL works
$path = $prettyUrl; // '/doc/build/build-system-documentation'
$docPath = substr($path, 5); // 'build/build-system-documentation'
echo "5. Doc path extracted: '$docPath'\n";

$file = doc_lookup_file_by_slug($docPath);
echo "6. File lookup result: '" . ($file ?: 'NOT FOUND') . "'\n";

if ($file) {
    echo "7. SUCCESS: This should work!\n";
} else {
    echo "7. FAILURE: Lookup failed\n";
}

// Let's also verify the browser behavior
echo "\n=== TESTING BROWSER URL SCENARIOS ===\n";
$testUrls = [
    '/doc/build/build-system-documentation',
    '/doc/build-2fbuild-system-documentation',
    '/doc/build%2Fbuild-system-documentation'
];

foreach ($testUrls as $url) {
    echo "Testing URL: '$url'\n";
    $docPath = substr($url, 5);
    echo "  Doc path: '$docPath'\n";
    
    // Apply the same decoding loop as index.php
    $prevDocPath = '';
    while ($prevDocPath !== $docPath) {
        $prevDocPath = $docPath;
        $docPath = urldecode($docPath);
    }
    echo "  After decoding: '$docPath'\n";
    
    $file = doc_lookup_file_by_slug($docPath);
    echo "  Result: " . ($file ?: 'NOT FOUND') . "\n\n";
}
