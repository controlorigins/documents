<?php
require_once 'includes/docs.php';

echo "Testing doc_pretty_url():\n";
$input = 'Build/Build System Documentation.md';
echo "Input: '$input'\n";
$result = doc_pretty_url($input);
echo "Output: '$result'\n";

// Let's also test the exact scenario
echo "\n=== Testing exact POST scenario ===\n";
$_POST['selected_file'] = 'Build%2FBuild+System+Documentation.md';
$selectedRel = urldecode($_POST['selected_file']);
echo "POST data (URL decoded): '$selectedRel'\n";
$prettyUrl = doc_pretty_url($selectedRel);
echo "Pretty URL result: '$prettyUrl'\n";

// Test what happens when we decode this back
echo "\n=== Testing URL parsing ===\n";
$path = '/doc/build/build-system-documentation';
echo "Expected path: '$path'\n";
$docPath = substr($path, 5);
echo "Doc path: '$docPath'\n";
$file = doc_lookup_file_by_slug($docPath);
echo "File lookup result: '$file'\n";

// Test the actual failing case
echo "\n=== Testing failing case ===\n";
$failingPath = '/doc/build-2fbuild-system-documentation';
echo "Failing path: '$failingPath'\n";
$failingDocPath = substr($failingPath, 5);
echo "Failing doc path: '$failingDocPath'\n";
$failingFile = doc_lookup_file_by_slug($failingDocPath);
echo "Failing file lookup: '" . ($failingFile ?: 'NOT FOUND') . "'\n";
