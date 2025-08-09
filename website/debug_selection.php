<?php
require_once 'includes/docs.php';

// Simulate what happens when a file is loaded
$_GET['file'] = 'ChatGPT/Sessions/Create PHP Joke Page.md';
$selectedFile = $_GET['file'];

echo "Selected file: '$selectedFile'\n";

// Test a few sample files to see if the comparison works
$testFiles = [
    'Overview.md',
    'Build/Build System Documentation.md', 
    'ChatGPT/Sessions/Create PHP Joke Page.md'
];

foreach ($testFiles as $testFile) {
    $relativePath = $testFile;
    $isSelected = ($selectedFile === $relativePath);
    echo "Test file: '$testFile' - Match: " . ($isSelected ? 'YES' : 'NO') . "\n";
    
    // Also show what URL this would generate
    $filePathNoExt = preg_replace('/\.md$/i', '', $testFile);
    $docUrl = doc_pretty_url($filePathNoExt);
    echo "  Would generate URL: '$docUrl'\n";
}
